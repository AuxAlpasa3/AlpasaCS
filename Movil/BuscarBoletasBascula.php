<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion2.php';



try {
    $sentencia = $Conexion->prepare("select distinct(t1.IdBoletas) as IdBoletas,t1.Placas,t1.Chofer,t10.Cliente,t7.TipoTransporte,t8.Descripcion from 
        t_boleta_enc as t1 
        inner join t_boleta_det as t2 on t1.Idboletas=t2.IdBoletasEnc
        LEFT JOIN t_tipoTransporte as t7 on t1.TipoTransporte=t7.IdTipoTransporte
        LEFT JOIN t_producto as t8 on t2.Producto=t8.IdProducto
        INNER JOIN t_cliente as t10 on t1.Cliente=t10.IdCliente
        WHERE t1.Estatus=0");
    $sentencia->execute();
    $listado = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($listado) > 0) {
        $listadoEncontrado = false;
        $resultado = array();
        
        foreach ($listado as $row) {
                
                $resultado = array(array(
                    'IdBoletas' => $row->IdBoletas,
                    'Placas' => $row->Placas,
                    'Chofer' => $row->Chofer,
                    'Cliente' => $row->Cliente,
                    'TipoTransporte' => $row->TipoTransporte,
                    'Descripcion' => $row->Descripcion
                ));
        }
        
    } else {
        $resultado = "No hay boletas disponibles por descargar";
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode("Error en el servidor: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}
?>