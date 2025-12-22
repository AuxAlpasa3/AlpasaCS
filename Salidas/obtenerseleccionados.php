<?PHP
header('Content-Type: application/json;  charset=UTF-8');
 Include '../api/db/conexion.php';

$idLote = $_GET['id'];
$idRemision = $_GET['idRemision'];
$MaterialNo = $_GET['MaterialNo'];
$IdAlmacen = $_GET['IdAlmacen'];


$sql = "SELECT t1.CodBarras,t3.NombreCliente,t1.MaterialNo,CONCAT(t4.Material,' ',t4.Shape) as Articulo, t7.NumRecinto,
  t1.Piezas From t_pasoSalida as t1
  INNER JOIN t_remision_encabezado as t2 on t1.IdRemision=t2.IdRemisionEncabezado
  INNER JOIN t_cliente as t3 on t2.Cliente=t3.IdCliente
  INNER JOIN t_articulo as t4 on t4.MaterialNo=t1.MaterialNo
  INNER JOIN t_remision_linea as t5 on t2.IdRemision=t5.IdRemision and t1.IdLinea=t5.IdLinea
  INNER JOIN t_almacen as t7 on t7.IdAlmacen=t2.Almacen
  where  t1.IdLinea=? and t1.IdRemision=? and t4.MaterialNo=? and t2.Almacen=? order by t1.CodBarras";

$stmt = $Conexion->prepare($sql);
$stmt->execute([$idLote, $idRemision, $MaterialNo,$IdAlmacen]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);

?>