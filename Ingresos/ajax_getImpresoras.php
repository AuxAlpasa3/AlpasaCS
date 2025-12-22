<?php
  include ('../api/db/conexion.php');

$IdAlmacen = $_POST['IdAlmacen'] ?? 0;

$stmt = $Conexion->prepare("SELECT NombreImpresora 
                            FROM t_impresoras 
                            WHERE Almacen = ?");
$stmt->execute([$IdAlmacen]);
$impresoras = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($impresoras as $i){
  echo '<option value="'.$i->NombreImpresora.'">'.$i->NombreImpresora.'</option>';
}
?>
