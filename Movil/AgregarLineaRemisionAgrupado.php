<?php
header('Content-Type: application/json; charset=UTF-8');
        
Include '../api/db/Conexion.php';

date_default_timezone_set('America/Monterrey');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $IdRemision = $_POST['IdRemision'];
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
    $IdLinea = $_POST['IdLinea'];
    $IdArticulo = $_POST['IdArticulo'];
    $Piezas = $_POST['Piezas'];
    $Cliente = $_POST['Cliente'];
    $IdRemisionAgrupada = $_POST['IdRemisionAgrupada'];
    $Almacen = $_POST['Almacen'];
    $Comentarios = $_POST['Comentarios'];
    $TipoCambio =  'INSERT'; 
    $Usuario = $_POST['IdUsuario']; 

    try {
       
        $Conexion->beginTransaction();

        $sentencia = $Conexion->prepare("INSERT INTO t_remision_linea 
        (IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef)
        VALUES 
        (?,?,?,?,?,?,?)");
        $resultado = $sentencia->execute([$IdRemision, $IdLinea, $IdArticulo, $Piezas, $Cliente, $Almacen, $IdRemisionEncabezado]);
        
        if($resultado){
            $IdRemisionLinea = $Conexion->lastInsertId();
            
            $sentencia_historial = $Conexion->prepare("INSERT INTO t_remision_linea_historial 
            (IdRemisionLinea, IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef, TipoCambio, Comentario, FechaCambio, Usuario)
            VALUES 
            (?,?,?,?,?,?,?,?,?,?,GETDATE(),?)");
            
            $resultado_historial = $sentencia_historial->execute([
                $IdRemisionLinea, 
                $IdRemision, 
                $IdLinea, 
                $IdArticulo, 
                $Piezas, 
                $Cliente, 
                $Almacen, 
                $IdRemisionEncabezado, 
                $TipoCambio, 
                $Comentarios, 
                $Usuario
            ]);
            
            if($resultado_historial){
                $Conexion->commit();
                $Mensaje = 'Agregado Correctamente.'; 
            } else {
                $Conexion->rollBack();
                $Mensaje = 'Error al agregar al historial.'; 
            }
        } else {
            $Conexion->rollBack();
            $Mensaje = 'Error al agregar la línea.'; 
        }
    } catch (Exception $e) {
        $Conexion->rollBack();
        $Mensaje = 'Error: ' . $e->getMessage(); 
    }
} else {
    $Mensaje = 'No es POST.'; 
}

echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);
?>