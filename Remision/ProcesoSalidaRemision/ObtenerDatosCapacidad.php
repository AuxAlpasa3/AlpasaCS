<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json');

$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'] ?? null;

if ($IdRemisionEncabezado) {
    try {
        $sentDatos = $Conexion->prepare("
            SELECT 
                SUM(COALESCE(t8.Alto, 0)) as AltoTotal,
                SUM(COALESCE(t8.Ancho, 0)) as AnchoTotal,
                SUM(COALESCE(t8.Largo, 0)) as LargoTotal
            FROM t_remision_encabezado as t1 
            LEFT JOIN t_remision_linea as t2 ON t1.IdRemisionEncabezado = t2.IdRemisionEncabezadoRef
            LEFT JOIN t_ingreso as t8 ON t2.CodBarras = t8.CodBarras
            WHERE t1.IdRemisionEncabezado = ?
        ");
        $sentDatos->execute([$IdRemisionEncabezado]);
        $datos = $sentDatos->fetch(PDO::FETCH_OBJ);
        
        // Asegurarse de que los valores no sean null
        $altoTotal = floatval($datos->AltoTotal) ?: 0;
        $anchoTotal = floatval($datos->AnchoTotal) ?: 0;
        $largoTotal = floatval($datos->LargoTotal) ?: 0;
        $volumenTotal = $altoTotal * $anchoTotal * $largoTotal;
        
        echo json_encode([
            'success' => true,
            'altoTotal' => $altoTotal,
            'anchoTotal' => $anchoTotal,
            'largoTotal' => $largoTotal,
            'volumenTotal' => $volumenTotal
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de remisión no proporcionado'
    ]);
}
?>