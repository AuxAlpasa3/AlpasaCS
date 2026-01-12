<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion2.php';

try {
    $sql = "SELECT DISTINCT(t1.IdBoletas) as IdBoletas,
                   t1.Placas,
                   t1.Chofer,
                   t10.Cliente,
                   t7.TipoTransporte,
                   t8.Descripcion
            FROM t_boleta_enc as t1 
            INNER JOIN t_boleta_det as t2 ON t1.Idboletas = t2.IdBoletasEnc
            LEFT JOIN t_tipoTransporte as t7 ON t1.TipoTransporte = t7.IdTipoTransporte
            LEFT JOIN t_producto as t8 ON t2.Producto = t8.IdProducto
            INNER JOIN t_cliente as t10 ON t1.Cliente = t10.IdCliente
            WHERE t1.Estatus = 0";
    
    $sentencia = $Conexion->prepare($sql);
    $sentencia->execute();
    $listado = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($listado) > 0) {
        $resultado = array();
        
        foreach ($listado as $row) {
            $resultado[] = array(
                'IdBoletas' => $row->IdBoletas,
                'Placas' => $row->Placas,
                'Chofer' => $row->Chofer,
                'Cliente' => $row->Cliente,
                'TipoTransporte' => $row->TipoTransporte,
                'Descripcion' => $row->Descripcion
            );
        }
        
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>