<?php
header('Content-Type: aplication/json;  charset=UTF-8');

Include '../api/db/conexion.php';

$sentencia = $Conexion->query("SELECT ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS IdPeriodicidad,
    CASE WHEN dias IN (0, NULL, ' ') THEN 'SIN PERIODICIDAD'  WHEN diasProduccion < dias THEN 'VIGENTE' WHEN diasProduccion > dias THEN 'VENCIDO' 
    END AS Periodicidad 
FROM t_inventario
where CASE 
        WHEN dias IN (0, NULL, ' ') THEN 'SIN PERIODICIDAD' 
        WHEN diasProduccion < dias THEN 'VIGENTE'
        WHEN diasProduccion > dias THEN 'VENCIDO' 
    END is not NULL
GROUP BY 
    CASE 
        WHEN dias IN (0, NULL, ' ') THEN 'SIN PERIODICIDAD' 
        WHEN diasProduccion < dias THEN 'VIGENTE'
        WHEN diasProduccion > dias THEN 'VENCIDO' 
    END;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>