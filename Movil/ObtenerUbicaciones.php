<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT t1.IdUbicacion, t1.Ubicacion, t1.Almacen 
                                    FROM t_ubicacion as t1
                                    WHERE t1.Almacen = $IdAlmacen;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>