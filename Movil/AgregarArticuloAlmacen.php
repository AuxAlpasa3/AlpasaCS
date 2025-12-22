<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

Include '../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $idArticulo = isset($data['IdArticulo']) ? trim($data['IdArticulo']) : '';
    $idAlmacen = isset($data['IdAlmacen']) ? trim($data['IdAlmacen']) : '';
    $idUsuario = isset($data['IdUsuario']) ? trim($data['IdUsuario']) : '';
    
    if (empty($idArticulo) || empty($idAlmacen)) {
        echo json_encode(['success' => false, 'message' => 'ID Artículo y Almacén son obligatorios']);
        exit;
    }
    
    try {
        $sqlCheck = "SELECT IdArticuloAlmacen FROM t_articuloAlmacen 
                     WHERE IdArticulo = :idArticulo AND IdAlmacen = :idAlmacen";
        $stmtCheck = $Conexion->prepare($sqlCheck);
        $stmtCheck->bindParam(':idArticulo', $idArticulo);
        $stmtCheck->bindParam(':idAlmacen', $idAlmacen);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetch()) {
            echo json_encode(['success' => true, 'message' => 'El artículo ya estaba asociado al almacén']);
            exit;
        }
        
        $sql = "INSERT INTO t_articuloAlmacen (
                    IdArticulo, 
                    IdAlmacen
                ) VALUES (
                    :idArticulo, 
                    :idAlmacen
                )";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->bindParam(':idArticulo', $idArticulo);
        $stmt->bindParam(':idAlmacen', $idAlmacen);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Artículo asociado al almacén con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asociar el artículo al almacén']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error general: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$Conexion = null;
?>