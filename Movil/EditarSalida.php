<?php
header('Content-Type: application/json; charset=UTF-8');
        
include '../api/db/Conexion.php';

date_default_timezone_set('America/Monterrey');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $IdTarja = $_POST['IdTarja'];
    $Transportista = trim($_POST['Transportista']);
    $Placas = trim($_POST['Placas']);
    $Chofer = trim($_POST['Chofer']);
    $IdAlmacen = trim($_POST['Almacen']);
    $Contenedor = isset($_POST['Contenedor']) ? trim($_POST['Contenedor']) : '';
    $Sellos = isset($_POST['Sellos']) ? trim($_POST['Sellos']) : '';
    $Caja = isset($_POST['Caja']) ? trim($_POST['Caja']) : '';
    $Tracto = isset($_POST['Tracto']) ? trim($_POST['Tracto']) : '';
    
    try {
        $Conexion->beginTransaction();
        
        $sentencia = $Conexion->prepare("UPDATE t_Salida SET Transportista = ?, Placas = ?, Chofer = ? WHERE IdTarja = ? AND Almacen = ?");
        $resultado1 = $sentencia->execute([$Transportista, $Placas, $Chofer, $IdTarja, $IdAlmacen]);
        
        $sentencia2 = $Conexion->prepare("UPDATE t_remision_Encabezado SET Transportista = ?, Placas = ?, Chofer = ?, Contenedor = ?, Sellos = ?, Tracto = ?, Caja = ? WHERE IdRemisionEncabezado IN (SELECT IdRemision FROM t_Salida WHERE IdTarja = ? AND Almacen = ?)");
        $resultado2 = $sentencia2->execute([$Transportista, $Placas, $Chofer, $Contenedor, $Sellos, $Tracto, $Caja, $IdTarja, $IdAlmacen]);
        
        if($resultado1 && $resultado2) {
            $Conexion->commit();
            $response = [
                'success' => true,
                'message' => 'Remisión modificada correctamente'
            ];
        } else {
            $Conexion->rollBack();
            $response = [
                'success' => false,
                'message' => 'Error al actualizar los datos'
            ];
        }
        
    } catch (Exception $e) {
        $Conexion->rollBack();
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Método no permitido. Se requiere POST.'
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>