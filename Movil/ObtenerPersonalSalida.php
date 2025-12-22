<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

include '../api/db/conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $IdTarjaSalida = isset($_GET['IdTarjaSalida']) ? $_GET['IdTarjaSalida'] : '';
    $IdAlmacen = isset($_GET['IdAlmacen']) ? $_GET['IdAlmacen'] : '';

    if (empty($IdTarjaSalida)) {
        echo json_encode([
            'success' => false,
            'message' => 'Se requiere el parámetro IdTarjaSalida'
        ]);
        exit;
    }

    try {
        $db = $Conexion;

        $query = "SELECT 
                    IdSalidaPersonal,
                    IdTarjaSalida,
                    IdEmpleado,
                    Nombre,
                    Rol,
                    CONVERT(varchar, FechaSalida, 120) as FechaSalida
                  FROM t_Salida_personal 
                  WHERE IdTarjaSalida = ? and IdAlmacen = ?
                  ORDER BY IdEmpleado";

        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $IdTarjaSalida);
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