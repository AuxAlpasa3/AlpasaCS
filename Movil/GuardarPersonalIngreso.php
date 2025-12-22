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

    $IdIngreso = isset($data['IdIngreso']) ? $data['IdIngreso'] : '';
    $Nombre = isset($data['Nombre']) ? $data['Nombre'] : '';
    $Rol = isset($data['Rol']) ? $data['Rol'] : '';
    $IdUsuario = isset($data['IdUsuario']) ? $data['IdUsuario'] : '';
    $Almacen = isset($data['Almacen']) ? $data['Almacen'] : '';

    if (empty($IdIngreso) || empty($Nombre) || empty($Rol) || empty($Almacen)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan campos obligatorios: IdIngreso, Nombre, Rol, Almacen'
        ]);
        exit;
    }

    try {
        $db = $Conexion;

        $queryConsecutivo = "SELECT ISNULL(MAX(IdEmpleado), 0) + 1 as Consecutivo 
                            FROM t_ingreso_personal 
                            WHERE IdTarjaIngreso = ? AND IdAlmacen = ?";

        $stmtConsecutivo = $db->prepare($queryConsecutivo);
        $stmtConsecutivo->bindParam(1, $IdIngreso);
        $stmtConsecutivo->bindParam(2, $Almacen);
        $stmtConsecutivo->execute();

        $resultado = $stmtConsecutivo->fetch(PDO::FETCH_ASSOC);
        $IdEmpleado = $resultado['Consecutivo'];

        $query = "INSERT INTO t_ingreso_personal 
                  (IdTarjaIngreso, IdEmpleado, Nombre, Rol, IdAlmacen, FechaIngreso) 
                  VALUES (?, ?, ?, ?, ?, GETDATE())";

        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $IdIngreso);
        $stmt->bindParam(2, $IdEmpleado);
        $stmt->bindParam(3, $Nombre);
        $stmt->bindParam(4, $Rol);
        $stmt->bindParam(5, $Almacen);

        if ($stmt->execute()) {
            $idIngresoPersonal = $db->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Personal guardado correctamente',
                'id' => $idIngresoPersonal,
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