<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Contar total
    $sqlCount = "SELECT COUNT(*) as total 
                 FROM t_ubicacion 
                 WHERE (Activo = 1 OR :search_all = 1)
                 AND (NomCorto LIKE :search OR Descripcion LIKE :search)";
    
    $stmtCount = $Conexion->prepare($sqlCount);
    $search_term = '%' . $search . '%';
    $search_all = 1;
    $stmtCount->bindParam(':search', $search_term);
    $stmtCount->bindParam(':search_all', $search_all, PDO::PARAM_INT);
    $stmtCount->execute();
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC);
    
    // Obtener datos paginados
    $sql = "SELECT 
                IdUbicacion as id, 
                NomCorto as text,
                NomCorto as nombre,
                Descripcion
            FROM t_ubicacion 
            WHERE (Activo = 1 OR :search_all = 1)
            AND (NomCorto LIKE :search OR Descripcion LIKE :search)
            ORDER BY NomCorto
            OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':search', $search_term);
    $stmt->bindParam(':search_all', $search_all, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agregar "Sin Ubicación" si hay búsqueda
    if (!empty($search) && stripos('sinubicacion', $search) !== false) {
        array_unshift($ubicaciones, [
            'id' => '0', 
            'text' => 'SinUbicacion', 
            'nombre' => 'SinUbicacion',
            'Descripcion' => 'Sin ubicación asignada'
        ]);
    }
    
    $resultado = [
        'results' => $ubicaciones,
        'pagination' => [
            'more' => ($offset + $limit) < $total['total']
        ],
        'total_count' => $total['total']
    ];
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
}
?>