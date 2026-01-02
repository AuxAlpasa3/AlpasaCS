<?php

include '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si es búsqueda por AJAX
$search = $_GET['q'] ?? '';

try {
    if (!empty($search)) {
        // Búsqueda con filtro
        $sql = "SELECT TOP 50 
                    IdPersonal as id, 
                    CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as nombre,
                    IdPersonal as codigo
                FROM t_personal 
                WHERE tipoPersonal = 1 
                AND (IdPersonal LIKE :search 
                     OR Nombre LIKE :search 
                     OR ApPaterno LIKE :search 
                     OR CONCAT(Nombre, ' ', ApPaterno) LIKE :search)
                ORDER BY Nombre, ApPaterno";
        
        $stmt = $Conexion->prepare($sql);
        $search_term = '%' . $search . '%';
        $stmt->bindParam(':search', $search_term);
    } else {
        // Cargar todos (limitado)
        $sql = "SELECT TOP 100 
                    IdPersonal as id, 
                    CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as nombre,
                    IdPersonal as codigo
                FROM t_personal 
                WHERE tipoPersonal = 1 
                ORDER BY Nombre, ApPaterno";
        
        $stmt = $Conexion->prepare($sql);
    }
    
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear para Select2
    $resultado = array();
    foreach ($personal as $p) {
        $resultado[] = [
            'id' => $p['id'],
            'text' => $p['nombre'] . ' (ID: ' . $p['codigo'] . ')',
            'nombre' => $p['nombre'],
            'codigo' => $p['codigo']
        ];
    }
    
    echo json_encode(['results' => $resultado], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar personal: ' . $e->getMessage()]);
}
?>