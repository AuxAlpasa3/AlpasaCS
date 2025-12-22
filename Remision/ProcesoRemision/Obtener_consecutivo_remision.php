<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idAlmacen']) && isset($_POST['recinto'])) {
    $idAlmacen = filter_var($_POST['idAlmacen'], FILTER_VALIDATE_INT);
    $recinto = htmlspecialchars(trim($_POST['recinto']));
    
    if ($idAlmacen && $recinto) {
        try {
            $consecutivoQuery = $Conexion->prepare("SELECT COALESCE(MAX(IdRemisionEncabezado), 0) + 1 AS siguiente 
                                                   FROM t_remision_encabezado 
                                                   WHERE Almacen = ?");
            $consecutivoQuery->execute([$idAlmacen]);
            $consecutivo = $consecutivoQuery->fetch(PDO::FETCH_OBJ);
            $siguienteId = $consecutivo->siguiente;
            
            $idRemisionFormateado = "REM-" . $recinto . "-" . str_pad($siguienteId, 2, '0', STR_PAD_LEFT);
            
            echo json_encode([
                'success' => true,
                'idRemision' => $idRemisionFormateado,
                'consecutivo' => $siguienteId,
                'recinto' => $recinto
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en obtener_consecutivo_remision.php: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error en la base de datos'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Datos inválidos'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido o datos incompletos'
    ]);
}
?>