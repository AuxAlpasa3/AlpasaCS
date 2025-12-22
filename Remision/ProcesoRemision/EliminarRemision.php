<?php
include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$IdRemisionEncabezado = filter_var($_POST['IdRemisionEncabezado'] ?? 0, FILTER_VALIDATE_INT);
$TipoRemision = filter_var($_POST['TipoRemision'] ?? 0, FILTER_VALIDATE_INT);
$user = filter_var($_POST['user'] ?? 0, FILTER_VALIDATE_INT);

try {
    $Conexion->beginTransaction();
    
    $sqlGetEncabezados = "SELECT IdRemisionEncabezado FROM t_remision_encabezado 
                          WHERE IdRemisionEncabezado = ? AND Estatus IN (0,1)";
    $stmtGetEncabezados = $Conexion->prepare($sqlGetEncabezados);
    $stmtGetEncabezados->execute([$IdRemisionEncabezado]);
    $encabezados = $stmtGetEncabezados->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($encabezados)) {
        $placeholders = str_repeat('?,', count($encabezados) - 1) . '?';
        
        $sqlDeleteLinea = "DELETE FROM t_remision_linea 
                          WHERE IdremisionEncabezadoref IN ($placeholders)";
        $stmtDeleteLinea = $Conexion->prepare($sqlDeleteLinea);
        $stmtDeleteLinea->execute($encabezados);
        
        $lineasEliminadas = $stmtDeleteLinea->rowCount();
        error_log("Líneas eliminadas: " . $lineasEliminadas);
    }
    
    $sqlDeleteEncabezado = "DELETE FROM t_remision_encabezado 
                           WHERE IdRemisionEncabezado = ? AND Estatus IN (0,1)";
    $stmtDeleteEncabezado = $Conexion->prepare($sqlDeleteEncabezado);
    $stmtDeleteEncabezado->execute([$IdRemisionEncabezado]);
    $encabezadosEliminados = $stmtDeleteEncabezado->rowCount();

    $sqlDeletePasoSalida = "DELETE FROM t_pasoSalida 
                           WHERE IdRemision = ? AND Estatus IN (0,1)";
    $stmtDeletePasoSalida = $Conexion->prepare($sqlDeletePasoSalida);
    $stmtDeletePasoSalida->execute([$IdRemisionEncabezado]);
    
    $Conexion->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Remisión eliminada correctamente'
    ]);
    
} catch (Exception $e) {
    $Conexion->rollBack();
    
    error_log("Error al eliminar remisión: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error al eliminar la remisión: ' . $e->getMessage()
    ]);
}
?>