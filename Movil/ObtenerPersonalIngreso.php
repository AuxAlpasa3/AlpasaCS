<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

include '../api/db/conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $IdTarjaIngreso = isset($_GET['IdTarjaIngreso']) ? $_GET['IdTarjaIngreso'] : '';
    $IdAlmacen = isset($_GET['IdAlmacen']) ? $_GET['IdAlmacen'] : '';

    if (empty($IdTarjaIngreso)) {
        echo json_encode([
            'success' => false,
            'message' => 'Se requiere el parámetro IdTarjaIngreso'
        ]);
        exit;
    }

    try {
        $db = $Conexion;

        $query = "SELECT 
                    IdIngresoPersonal,
                    IdTarjaIngreso,
                    IdEmpleado,
                    Nombre,
                    Rol,
                    CONVERT(varchar, FechaIngreso, 120) as FechaIngreso
                  FROM t_ingreso_personal 
                  WHERE IdTarjaIngreso = ? and IdAlmacen = ?
                  ORDER BY IdEmpleado";

        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $IdTarjaIngreso);
        $stmt->bindParam(2, $IdAlmacen);
        $stmt->execute();

        $personal = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $personal[] = $row;
        }

        echo json_encode([
            'success' => true,
            'data' => $personal,
            'count' => count($personal)
        ]);

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