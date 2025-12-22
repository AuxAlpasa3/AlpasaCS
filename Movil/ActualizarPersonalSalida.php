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

    $IdsalidaPersonal = isset($data['IdsalidaPersonal']) ? $data['IdsalidaPersonal'] : '';
    $Nombre = isset($data['Nombre']) ? $data['Nombre'] : '';
    $Rol = isset($data['Rol']) ? $data['Rol'] : '';
    $IdUsuario = isset($data['IdUsuario']) ? $data['IdUsuario'] : '';
    $Almacen = isset($data['Almacen']) ? $data['Almacen'] : '';

    // Validación de campos obligatorios
    $camposRequeridos = ['IdsalidaPersonal', 'Nombre', 'Rol', 'IdUsuario', 'Almacen'];
    $camposFaltantes = [];

    foreach ($camposRequeridos as $campo) {
        if (empty(trim($data[$campo] ?? ''))) {
            $camposFaltantes[] = $campo;
        }
    }

    if (!empty($camposFaltantes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan campos obligatorios: ' . implode(', ', $camposFaltantes)
        ]);
        exit;
    }

    try {
        $db = $Conexion;

        $queryCheck = "SELECT IdsalidaPersonal FROM t_salida_personal WHERE IdsalidaPersonal = ?";
        $stmtCheck = $db->prepare($queryCheck);
        $stmtCheck->bindParam(1, $IdsalidaPersonal);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Registro de personal no encontrado'
            ]);
            exit;
        }

        // Actualizar el registro
        $query = "UPDATE t_salida_personal 
                  SET Nombre = ?, 
                      Rol = ?
                  WHERE IdsalidaPersonal = ?";

        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $Nombre);
        $stmt->bindParam(2, $Rol);
        $stmt->bindParam(3, $IdUsuario);
        $stmt->bindParam(4, $Almacen);
        $stmt->bindParam(5, $IdsalidaPersonal);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Personal actualizado correctamente',
                    'data' => [
                        'IdsalidaPersonal' => $IdsalidaPersonal,
                        'Nombre' => $Nombre,
                        'Rol' => $Rol,
                        'Almacen' => $Almacen
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'No se realizaron cambios en el registro',
                    'data' => [
                        'IdsalidaPersonal' => $IdsalidaPersonal
                    ]
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el personal en la base de datos'
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