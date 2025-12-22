<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$Periodicidad = ($_GET['Periodicidad'] ?? null) === 'undefined' ? null : ($_GET['Periodicidad'] ?? null);
$CodBarras = ($_GET['CodBarras'] ?? null) === 'undefined' ? null : ($_GET['CodBarras'] ?? null);
$MaterialNo = ($_GET['MaterialNo'] ?? null) === 'undefined' ? null : ($_GET['MaterialNo'] ?? null);
$IdCliente = ($_GET['IdCliente'] ?? null) === 'undefined' ? null : ($_GET['IdCliente'] ?? null);

$sql = "SELECT t1.CodBarras,t1.MaterialNo , t1.Piezas, t4.NombreCliente as Cliente, t2.estadomaterial as EstadoMaterial, (CASE WHEN dias IN (0, NULL, ' ') THEN 'SIN PERIODICIDAD'  WHEN diasProduccion < dias THEN 'VIGENTE' WHEN diasProduccion > dias THEN 'VENCIDO' 
    END) AS Periodicidad ,FORMAT(t1.FechaProduccion, 'yyyy-MM-dd') as FechaProduccion, t5.Ubicacion
FROM t_inventario as t1 
INNER JOIN t_estadoMaterial as t2 ON t1.EstadoMaterial = t2.IdEstadoMaterial 
LEFT JOIN t_cliente t4 on t4.IdCliente = t1.Cliente 
inner join t_ubicacion t5 on t1.IdUbicacion=t5.IdUbicacion
WHERE t1.Piezas > 0 and t1.EnProceso=0";

$params = [];

if ($CodBarras != null) {
    $sql .= " AND t1.CodBarras LIKE :CodBarras"; 
    $params[':CodBarras'] = '%' . $CodBarras . '%'; 
}

if ($MaterialNo != null) {
    $sql .= " AND t1.MaterialNo = :MaterialNo";
    $params[':MaterialNo'] = $MaterialNo;
}

if ($IdCliente != null) {
    $sql .= " AND t1.Cliente like % :IdCliente %";
    $params[':IdCliente'] = $IdCliente;
}

if ($Periodicidad != null) {
    $sql .= " AND (case when T1.dias=0 THEN 'SIN PERIODICIDAD' when T1.diasProduccion<T1.dias then 'VIGENTE' 
        WHEN T1.diasProduccion >T1.dias THEN 'VENCIDO' END) = :Periodicidad";
    $params[':Periodicidad'] = $Periodicidad;
}

$sql .= " ORDER BY CodBarras";


$sentencia = $Conexion->prepare($sql);
$sentencia->execute($params);
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>