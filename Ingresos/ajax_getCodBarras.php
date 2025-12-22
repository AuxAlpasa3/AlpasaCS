<?php
include ('../api/db/conexion.php');

$IdAlmacen = $_POST['IdAlmacen'] ?? 0;

$stmt = $Conexion->prepare("
    SELECT DISTINCT t1.CodBarras, t2.NumRecinto
    FROM t_ingreso AS t1
    INNER JOIN t_almacen AS t2 ON t1.Almacen = t2.IdAlmacen
    WHERE t1.Almacen = ?
");
$stmt->execute([$IdAlmacen]);
$codigos = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($codigos as $c){
    echo '<option value="'.$c->CodBarras.'">'.$c->NumRecinto."-".sprintf("%06d",$c->CodBarras).'</option>';
}
?>
