<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

$IdVisita = $_POST['IdVisita'] ?? '';

if (empty($IdVisita)) {
    $response['message'] = 'ID de visita requerido';
    echo json_encode($response);
    exit;
}

try {
    // Primero obtener información para el mensaje
    $sqlInfo = "SELECT p.NombreProveedor 
                FROM Visitas v
                JOIN t_proveedores p ON v.IdProveedor = p.IdProveedor
                WHERE v.IdVisita = ?";
    
    $stmtInfo = $Conexion->prepare($sqlInfo);
    $stmtInfo->execute([$IdVisita]);
    $visita = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    
    if (!$visita) {
        $response['message'] = 'Visita no encontrada';
        echo json_encode($response);
        exit;
    }
    
    // Eliminar visita
    $sql = "DELETE FROM Visitas WHERE IdVisita = ?";
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$IdVisita]);
    
    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = 'Visita de ' . $visita['NombreProveedor'] . ' eliminada correctamente';
    } else {
        $response['message'] = 'No se pudo eliminar la visita';
    }
    
} catch (PDOException $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
$Conexion = null;
?>