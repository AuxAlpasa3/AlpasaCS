<?php
require_once "../../api/db/conexion.php"; 

header('Content-Type: application/json');

if (!isset($_GET['codBarras'])) {
    echo json_encode(['success' => false, 'message' => 'Código de barras no proporcionado']);
    exit;
}

$codBarras = $_GET['codBarras'];

try {
    $stmt = $pdo->prepare("SELECT * FROM t_ingreso WHERE CodBarras = ?");
    $stmt->execute([$codBarras]);
    $ingreso = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ingreso) {
        echo json_encode(['success' => true, 'ingreso' => $ingreso]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos']);
}