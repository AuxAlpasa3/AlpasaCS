<?php
header('Content-Type: application/json; charset=UTF-8');
Include '../api/db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $IdLinea = $_POST["IdLinea"];
    $IdRemision = $_POST["IdRemision"]; 
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
    $Almacen = $_POST['Almacen'];
    $Comentarios = trim($_POST['Comentarios']);
    $Usuario = $_POST['Usuario'];

    // Validar que los comentarios no estén vacíos
    if (empty($Comentarios)) {
        echo json_encode("Los comentarios son obligatorios para eliminar una línea.", JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        // Iniciar transacción
        $Conexion->beginTransaction();

        // Primero obtener los datos actuales de la línea antes de eliminarla
        $sentencia_select = $Conexion->prepare("SELECT * FROM t_remision_linea WHERE IdLinea = ? AND IdRemision = ? AND IdRemisionEncabezadoRef = ? AND Almacen = ?");
        $sentencia_select->execute([$IdLinea, $IdRemision, $IdRemisionEncabezado, $Almacen]);
        $linea_data = $sentencia_select->fetch(PDO::FETCH_ASSOC);
        
        if (!$linea_data) {
            $Conexion->rollBack();
            echo json_encode("No se encontró el registro a eliminar.", JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Insertar en el historial antes de eliminar
        $sentencia_historial = $Conexion->prepare("INSERT INTO t_remision_linea_historial 
            (IdRemisionLinea, IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, 
             IdRemisionEncabezadoRef, TipoCambio, Comentario, FechaCambio, Usuario)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, 'DELETE', ?, GETDATE(), ?)");
        
        $resultado_historial = $sentencia_historial->execute([
            $linea_data['IdRemisionLinea'] ?? null,
            $linea_data['IdRemision'],
            $linea_data['IdLinea'],
            $linea_data['IdArticulo'],
            $linea_data['Piezas'],
            $linea_data['Cliente'],
            $linea_data['Almacen'],
            $linea_data['IdRemisionEncabezadoRef'],
            $Comentarios,
            $Usuario
        ]);

        if (!$resultado_historial) {
            $Conexion->rollBack();
            echo json_encode("Error al agregar al historial.", JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Ahora aplicar el DELETE físico
        $sentencia_delete = $Conexion->prepare("DELETE FROM t_remision_linea WHERE IdLinea = ? AND IdRemision = ? AND IdRemisionEncabezadoRef = ? AND Almacen = ?");
        $resultado_delete = $sentencia_delete->execute([$IdLinea, $IdRemision, $IdRemisionEncabezado, $Almacen]);
        
        if ($resultado_delete) {
            // Actualizar los IdLinea de las líneas siguientes
            $sentencia_update = $Conexion->prepare("UPDATE t_remision_linea SET IdLinea = IdLinea - 1 WHERE IdLinea > ? AND IdRemision = ? AND IdRemisionEncabezadoRef = ? AND Almacen = ?");
            $resultado_update = $sentencia_update->execute([$IdLinea, $IdRemision, $IdRemisionEncabezado, $Almacen]);
            
            if ($resultado_update) {
                $Conexion->commit();
                $mensaje = "Eliminado correctamente.";  
            } else {
                $Conexion->rollBack();
                $mensaje = "Error al actualizar los números de línea.";      
            }
        } else {
            $Conexion->rollBack();
            $mensaje = "Ha ocurrido un error al intentar eliminar, intente de nuevo.";      
        }
    } catch (Exception $e) {
        $Conexion->rollBack();
        $mensaje = "Error: " . $e->getMessage();      
    }
} else {
    $mensaje = "No es POST";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
?>