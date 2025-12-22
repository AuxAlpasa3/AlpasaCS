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

    $IdSalida = isset($data['IdSalida']) ? $data['IdSalida'] : '';
    $Nombre = isset($data['Nombre']) ? $data['Nombre'] : '';
    $Rol = isset($data['Rol']) ? $data['Rol'] : '';
    $IdUsuario = isset($data['IdUsuario']) ? $data['IdUsuario'] : '';
    $Almacen = isset($data['Almacen']) ? $data['Almacen'] : '';

    if (empty($IdSalida) || empty($Nombre) || empty($Rol) || empty($Almacen)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan campos obligatorios: IdSalida, Nombre, Rol, Almacen'
        ]);
        exit;
    }

    try {
        $db = $Conexion;

        $queryConsecutivo = "SELECT ISNULL(MAX(IdEmpleado), 0) + 1 as Consecutivo 
                            FROM t_Salida_personal 
                            WHERE IdTarjaSalida = ? AND IdAlmacen = ?";

        $stmtConsecutivo = $db->prepare($queryConsecutivo);
        $stmtConsecutivo->bindParam(1, $IdSalida);
        $stmtConsecutivo->bindParam(2, $Almacen);
        $stmtConsecutivo->execute();

        $resultado = $stmtConsecutivo->fetch(PDO::FETCH_ASSOC);
        $IdEmpleado = $resultado['Consecutivo'];

        $query = "INSERT INTO t_Salida_personal 
                  (IdTarjaSalida, IdEmpleado, Nombre, Rol, IdAlmacen, FechaSalida) 
                  VALUES (?, ?, ?, ?, ?, GETDATE())";

        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $IdSalida);
        $stmt->bindParam(2, $IdEmpleado);
        $stmt->bindParam(3, $Nombre);
        $stmt->bindParam(4, $Rol);
        $stmt->bindParam(5, $Almacen);

        if ($stmt->execute()) {
            $idSalidaPersonal = $db->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Personal guardado correctamente',
                'id' => $idSalidaPersonal,
                'IdEmpleado' => $IdEmpleado,
                'Almacen' => $Almacen
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar el personal en la base de datos'
            ]);
        }

    } catch (Exception $e) {
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