<?PHP
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'];
$IdCliente = $_GET['IdCliente'];
$IdAlmacen = $_GET['IdAlmacen'];

$sql = "SELECT t1.CodBarras,t3.NombreCliente,t1.MaterialNo,CONCAT(t4.Material,' ',t4.Shape) as Articulo, t7.NumRecinto,
  t1.Piezas,t8.Origen as Destino From t_pasoSalida as t1
  INNER JOIN t_remision_encabezado as t2 on t1.IdRemision=t2.IdRemisionEncabezado 
  INNER JOIN t_cliente as t3 on t2.Cliente=t3.IdCliente
  INNER JOIN t_articulo as t4 on t4.MaterialNo=t1.MaterialNo
  INNER JOIN t_remision_linea as t5 on t2.IdRemision=t5.IdRemision and t1.IdLinea=t5.IdLinea and t1.CodBarras=t5.CodBarras
  INNER JOIN t_almacen as t7 on t7.IdAlmacen=t2.Almacen
  INNER JOIN t_inventario as t8 on t1.CodBarras=t8.CodBarras and t2.Almacen=t8.Almacen
  where t2.IdRemisionEncabezado=? and IdAlmacen=? and IdCliente=? and t8.Piezas>0 order by t1.CodBarras";

$stmt = $Conexion->prepare($sql);
$stmt->execute([ $IdRemisionEncabezado, $IdAlmacen, $IdCliente]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);

?>