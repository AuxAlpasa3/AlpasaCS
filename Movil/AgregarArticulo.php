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
    
    $materialNo = isset($data['MaterialNo']) ? trim($data['MaterialNo']) : '';
    $material = isset($data['Material']) ? trim($data['Material']) : '';
    $tipoMaterial = intval(trim($data['TipoMaterial']));
    $uom = intval(trim($data['UoM']));
    $reglaEstiba = intval(trim($data['ReglaEstiba']));
    $tipoEmbalaje = intval(trim($data['TipoEmbalaje']));
    $netWeightUnit = floatval(trim($data['NetWeightUnit']));
    $periodicidad = 1;
        
    
    if (empty($materialNo) || empty($material) || empty($tipoMaterial) || empty($uom) || empty($reglaEstiba) || empty($tipoEmbalaje)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben ser llenados']);
        exit;
    }
    
    try {
        $sqlCheck = "SELECT IdArticulo FROM t_articulo WHERE MaterialNo = :materialNo";
        $stmtCheck = $Conexion->prepare($sqlCheck);
        $stmtCheck->bindParam(':materialNo', $materialNo);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El MaterialNo ya existe en el sistema']);
            exit;
        }
        
        $sql = "INSERT INTO t_articulo (
                    MaterialNo, 
                    Material, 
                    TipoMaterial, 
                    UoM, 
                    ReglaEstiba, 
                    TipoEmbalaje, 
                    NetWeightUnit,
                    Periodicidad
                ) VALUES (
                    :materialNo, 
                    :material, 
                    :tipoMaterial, 
                    :uom, 
                    :reglaEstiba, 
                    :tipoEmbalaje, 
                    :netWeightUnit,
                    :Periodicidad)";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->bindParam(':materialNo', $materialNo);
        $stmt->bindParam(':material', $material);
        $stmt->bindParam(':tipoMaterial', $tipoMaterial);
        $stmt->bindParam(':uom', $uom);
        $stmt->bindParam(':reglaEstiba', $reglaEstiba);
        $stmt->bindParam(':tipoEmbalaje', $tipoEmbalaje);
        $stmt->bindParam(':netWeightUnit', $netWeightUnit);
        $stmt->bindParam(':Periodicidad', $periodicidad);
        
        if ($stmt->execute()) {
            $idArticulo = $Conexion->lastInsertId();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Artículo agregado con éxito',
                'IdArticulo' => $idArticulo
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar el artículo']);
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