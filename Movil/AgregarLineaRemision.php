<?php
header('Content-Type: application/json; charset=UTF-8');
        
Include '../api/db/Conexion.php';

date_default_timezone_set('America/Monterrey');

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requiredFields = ['IdRemision', 'IdRemisionEncabezado', 'IdArticulo', 'Cantidad', 'IdUsuario'];
    
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $response['message'] = "Campo requerido faltante: $field";
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    $IdRemision = $_POST['IdRemision'];
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
    $IdArticulo = $_POST['IdArticulo'];
    $Piezas = isset($_POST['Piezas']) ? $_POST['Piezas'] : 0;
    $Booking = isset($_POST['Booking']) ? $_POST['Booking'] : '';
    $Cliente = isset($_POST['Cliente']) ? $_POST['Cliente'] : '';
    $Almacen = isset($_POST['Almacen']) ? $_POST['Almacen'] : '';
    $Comentarios = isset($_POST['Comentarios']) ? $_POST['Comentarios'] : '';
    $TipoCambio = 'INSERT'; 
    $Usuario = $_POST['IdUsuario']; 
    $Cantidad = intval($_POST['Cantidad']); 

    try {
        $Conexion->beginTransaction();

        $queryMaxIdLinea = $Conexion->prepare("SELECT ISNULL(MAX(IdLinea), 0) as MaxIdLinea FROM t_remision_linea WHERE IdRemision = ?");
        $queryMaxIdLinea->execute([$IdRemision]);
        $resultMax = $queryMaxIdLinea->fetch(PDO::FETCH_ASSOC);
        
        $IdLinea = intval($resultMax['MaxIdLinea']) + 1;

        $sentencia = $Conexion->prepare("INSERT INTO t_remision_linea 
        (IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef, Booking)
        VALUES 
        (?,?,?,?,?,?,?,?)");

        $sentencia_historial = $Conexion->prepare("INSERT INTO t_remision_linea_historial 
        (IdRemisionLinea, IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef, TipoCambio, Comentario, FechaCambio, Usuario, Booking)
        VALUES 
        (?,?,?,?,?,?,?,?,?,?,GETDATE(),?,?)");

        $registrosInsertados = 0;
        $errores = [];

        for ($i = 0; $i < $Cantidad; $i++) {
            $resultado = $sentencia->execute([
                $IdRemision, 
                $IdLinea, 
                $IdArticulo, 
                $Piezas, 
                $Cliente, 
                $Almacen, 
                $IdRemisionEncabezado, 
                $Booking
            ]);
            
            if ($resultado) {
                $IdRemisionLinea = $Conexion->lastInsertId();
                
                // Insertar en t_remision_linea_historial
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
                    $Usuario,
                    $Booking
                ]);
                
                if ($resultado_historial) {
                    $registrosInsertados++;
                    // Incrementar IdLinea para el siguiente registro si hay múltiples
                    $IdLinea++;
                } else {
                    $errores[] = "Error al insertar en historial para el registro " . ($i + 1);
                }
            } else {
                $errores[] = "Error al insertar en línea para el registro " . ($i + 1);
            }
        }

        if (empty($errores)) {
            $Conexion->commit();
            $response['success'] = true;
            $response['message'] = "Se agregaron correctamente $registrosInsertados registros.";
            $response['data'] = [
                'registrosInsertados' => $registrosInsertados,
                'ultimoIdLinea' => $IdLinea - 1 
            ];
        } else {
            $Conexion->rollBack();
            $response['message'] = "Se insertaron $registrosInsertados de $Cantidad registros. Errores: " . implode(', ', $errores);
        }

    } catch (Exception $e) {
        $Conexion->rollBack();
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido. Se requiere POST.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>