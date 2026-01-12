<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion2.php';

try {
        $sql = "SELECT t1.IdBoletas,
       t1.Placas,
       t1.Chofer,
       t10.Cliente,
       t7.TipoTransporte,
       YEAR(t1.FechaCita) as año,
       (SELECT STRING_AGG(Descripcion, ', ') WITHIN GROUP (ORDER BY Descripcion ASC)
        FROM (SELECT DISTINCT t8.Descripcion
              FROM t_boleta_det t2b
              LEFT JOIN t_producto t8 ON t2b.Producto = t8.IdProducto
              WHERE t2b.IdBoletasEnc = t1.Idboletas) AS productos) as Descripcion,
       t9.Transportista,
       t11.Ubicacion as Origen,
       t12.Ubicacion as Destino,
       (SELECT STRING_AGG(Placas, ', ') WITHIN GROUP (ORDER BY Placas ASC)
        FROM (SELECT DISTINCT t2c.Placas
              FROM t_boleta_det t2c
              WHERE t2c.IdBoletasEnc = t1.Idboletas) AS placas) as PlacasRemolque
FROM t_boleta_enc as t1 
LEFT JOIN t_tipoTransporte as t7 ON t1.TipoTransporte = t7.IdTipoTransporte
LEFT JOIN t_transportista as t9 on t1.Transportista = t9.IdTransportista
INNER JOIN t_cliente as t10 ON t1.Cliente = t10.IdCliente
INNER JOIN t_ubicacion as t11 on t1.Origen = t11.IdUbicacion
INNER JOIN t_ubicacion as t12 on t1.Destino = t12.IdUbicacion
WHERE t1.Estatus = 0  AND t1.FechaCita >= CAST(GETDATE() AS DATE) 
  AND t1.FechaCita < DATEADD(DAY, 1, CAST(GETDATE() AS DATE))  
GROUP BY t1.IdBoletas, t1.Placas, t1.Chofer, t10.Cliente, 
         t7.TipoTransporte, t9.Transportista, t11.Ubicacion, t12.Ubicacion,t1.FechaCita";
    
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
                'Descripcion' => $row->Descripcion,
                'Transportista' => $row->Transportista,
                'Origen' => $row->Origen,
                'Destino' => $row->Destino,
                'PlacasRemolque' => $row->PlacasRemolque
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