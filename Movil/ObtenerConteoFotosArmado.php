<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

Include '../api/db/Conexion.php';

$IdTarja = isset($_GET['IdArmado']) ? $_GET['IdArmado'] : '';

if(empty($IdTarja)) {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $consulta = $Conexion->prepare("SELECT COUNT(*) as count FROM t_fotografias_Encabezado as t1 inner join
t_fotografias_detalle as t2 on t1.IdFotografias=t2.idfotografiaref WHERE t1.IdTarja = :IdTarja and t1.Tipo=2s and t1.Almacen= :IdAlmacen");
    $consulta->bindParam(':IdTarja', $IdTarja);
    $consulta->execute();
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['count' => $resultado['count']]);
} catch(Exception $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
}
?>