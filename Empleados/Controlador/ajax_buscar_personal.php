<?php

include '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Primero contar total
    $sqlCount = "SELECT COUNT(*) as total 
                 FROM t_personal 
                 WHERE tipoPersonal = 1 
                 AND (IdPersonal LIKE :search 
                      OR Nombre LIKE :search 
                      OR ApPaterno LIKE :search 
                      OR ApMaterno LIKE :search 
                      OR CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) LIKE :search)";
    
    $stmtCount = $Conexion->prepare($sqlCount);
    $search_term = '%' . $search . '%';
    $stmtCount->bindParam(':search', $search_term);
    $stmtCount->execute();
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC);
    
    // Luego obtener datos paginados
    $sql = "SELECT 
                IdPersonal as id, 
                CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as text,
                IdPersonal as codigo,
                Nombre,
                ApPaterno,
                ApMaterno
            FROM t_personal 
            WHERE tipoPersonal = 1 
            AND (IdPersonal LIKE :search 
                 OR Nombre LIKE :search 
                 OR ApPaterno LIKE :search 
                 OR ApMaterno LIKE :search 
                 OR CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) LIKE :search)
            ORDER BY Nombre, ApPaterno
            OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':search', $search_term);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear para Select2
    $resultado = [
        'results' => $personal,
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