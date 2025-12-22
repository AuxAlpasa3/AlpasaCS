<?php
include ('../api/db/conexion.php');

$IdAlmacen = $_POST['IdAlmacen'] ?? 0;

$stmt = $Conexion->prepare("
    SELECT DISTINCT t1.IdTarja, t2.NumRecinto
    FROM t_ingreso AS t1
    INNER JOIN t_almacen AS t2 ON t1.Almacen = t2.IdAlmacen
    WHERE t1.Almacen = ? order by IdTarja desc
");
$stmt->execute([$IdAlmacen]);
$codigos = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($codigos as $c){
    echo '<option value="'.$c->IdTarja.'">'.'ALP'.$c->NumRecinto.'-ING-'.sprintf("%04d", $c->IdTarja).'</option>';
}
?>
