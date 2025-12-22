<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';
$IdUsuario = $_GET['IdUsuario'];
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT IdImpresora, NombreImpresora from t_impresoras as t1 
  INNER JOIN t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen
      where t2.IdUsuario=$IdUsuario and t1.Almacen=$IdAlmacen;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
  $Query = "No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>