<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verificar_contrasena') {
    $userId = $_POST['user_id'];
    $password = $_POST['password'];
try {
    $stmt = $Conexion->prepare("SELECT Contrasenia FROM t_usuario WHERE IdUsuario = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['Contrasenia'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
} catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Solicitud inválida']);