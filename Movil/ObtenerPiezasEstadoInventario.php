<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$IdUbicacion = ($_GET['IdUbicacion'] ?? null) === 'undefined' ? null : ($_GET['IdUbicacion'] ?? null);
$MaterialNo = ($_GET['MaterialNo'] ?? null) === 'undefined' ? null : ($_GET['MaterialNo'] ?? null);
$IdCliente = ($_GET['IdCliente'] ?? null) === 'undefined' ? null : ($_GET['IdCliente'] ?? null);
$IdAlmacen = ($_GET['IdAlmacen'] ?? null) === 'undefined' ? null : ($_GET['IdAlmacen'] ?? null);

$sql = "SELECT 
    t1.CodBarras,
    t1.MaterialNo, 
    t1.Piezas, 
    t1.Cliente, 
    t4.NombreCliente, 
    STUFF((SELECT ', ' + tem.EstadoMaterial 
           FROM STRING_SPLIT(t1.EstadoMaterial, ',') estado 
           INNER JOIN t_estadoMaterial tem ON CAST(estado.value AS INT) = tem.IdEstadoMaterial 
           FOR XML PATH('')), 1, 2, '') as EstadoMaterial, 
    FORMAT(t1.FechaProduccion, 'dd-MM-yyyy') as FechaProduccion,t2.NumRecinto
FROM t_inventario as t1 
INNER JOIN t_almacen as t2 on t1.Almacen=t2.IdAlmacen
LEFT JOIN t_cliente t4 on t4.IdCliente = t1.Cliente 
WHERE t1.Piezas > 0 and t1.EnProceso=0";

$params = [];

if ($IdUbicacion != null) {
    $sql .= " AND t1.IdUbicacion = :IdUbicacion";
    $params[':IdUbicacion'] = $IdUbicacion;
}

if ($MaterialNo != null) {
    $sql .= " AND t1.MaterialNo = :MaterialNo";
    $params[':MaterialNo'] = $MaterialNo;
}

if ($IdCliente != null) {
    $sql .= " AND t1.Cliente = :IdCliente";
    $params[':IdCliente'] = $IdCliente;
}

if ($IdAlmacen != null) {
    $sql .= " AND t1.Almacen = :IdAlmacen";
    $params[':IdAlmacen'] = $IdAlmacen;
}

$sql .= " ORDER BY CodBarras";

$sentencia = $Conexion->prepare($sql);
$sentencia->execute($params);
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>