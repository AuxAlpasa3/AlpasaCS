<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

include('../api/db/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        $data = $_POST;
    }

    $IdSalidaPersonal = isset($data['IdSalidaPersonal']) ? $data['IdSalidaPersonal'] : '';
    $IdAlmacen = isset($data['IdAlmacen']) ? $data['IdAlmacen'] : '';
    $IdEmpleado = isset($data['IdEmpleado']) ? $data['IdEmpleado'] : '';
    $IdTarjasalida = isset($data['IdTarjasalida']) ? $data['IdTarjasalida'] : '';
    
    if (empty($IdSalidaPersonal)) {
        echo json_encode([
            'success' => false,
            'message' => 'Se requiere el parámetro IdSalidaPersonal'
        ]);
        exit;
    }

    try {
        $db = $Conexion;
        $db->beginTransaction();

        $deleteQuery = "DELETE FROM t_salida_personal WHERE IdSalidaPersonal = ? AND IdAlmacen = ?";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(1, $IdSalidaPersonal);
        $deleteStmt->bindParam(2, $IdAlmacen);

        if (!$deleteStmt->execute()) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el personal'
            ]);
            exit;
        }

        $updateQuery = "UPDATE t_salida_personal 
                       SET IdEmpleado = IdEmpleado - 1 
                       WHERE IdEmpleado > ? AND IdAlmacen = ? and IdTarjasalida=?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(1, $IdEmpleado);
        $updateStmt->bindParam(2, $IdAlmacen);
        $updateStmt->bindParam(3, $IdTarjasalida);


        if (!$updateStmt->execute()) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la secuencia de IDs'
            ]);
            exit;
        }

        // Confirmar la transacción
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Personal eliminado correctamente y secuencia de IDs actualizada'
        ]);

     } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>