<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT 
    t1.IdTarja as IdTarja,
    t1.CodBarras,
    t3.MaterialNo,
    CONCAT(t3.Material, t3.Shape) as MaterialShape,
    t2.Piezas,
    t2.NetWeight,
    t2.GrossWeight,
    t4.Ubicacion,
    t1.EstadoMercancia,
    t1.FechaIngreso,
    t1.IdRemision,
    t5.NombreCliente
FROM t_ingreso t1
INNER JOIN t_inventario t2 ON t2.CodBarras = t1.CodBarras
LEFT JOIN t_articulo t3 ON t3.IdArticulo = t1.IdArticulo
LEFT JOIN t_ubicacion t4 ON t1.IdUbicacion = t4.IdUbicacion
LEFT JOIN t_cliente t5 ON t1.Cliente = t5.IdCliente
WHERE t1.Almacen = $IdAlmacen  AND t2.EnProceso = 0 and t2.Piezas>0
ORDER BY t1.FechaIngreso DESC");

$datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

if ($datos) {
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
} else {
    echo json_encode(
        array(
            'Mensaje' => 'No se encontró información',
        ),
        JSON_UNESCAPED_UNICODE
    );
}
?>