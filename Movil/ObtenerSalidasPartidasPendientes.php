<?PHP
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'];
$IdAlmacen = $_GET['IdAlmacen'];

$sql = $Conexion->query("SELECT t2.IdLinea,t5.NombreCliente,t3.MaterialNo, Concat(t3.Material,' ',t3.Shape) as Articulo, t2.Piezas, t1.IdRemision, 
t2.Piezas-sum(isnull(t4.Piezas,0)) as Faltan,sum(isnull(t4.Piezas,0)) as totales,t1.IdRemisionEncabezado, t6.NumRecinto
FROM t_remision_encabezado as t1 
INNER JOIN t_remision_linea as t2 on t1.IdRemision=t2.IdRemision 
INNER JOIN t_articulo as t3 on t2.IdArticulo =t3.IdArticulo 
LEFT JOIN t_pasoSalida as t4 on t1.IdRemisionEncabezado=t4.IdRemision and t2.IdLinea=t4.IdLinea
INNER JOIN t_cliente as t5 on t1.Cliente=t5.IdCliente
INNER JOIN t_almacen as t6 on t1.Almacen=t6.IdAlmacen
where t1.TipoRemision=2 and t1.IdRemisionEncabezado=$IdRemisionEncabezado AND t1.Almacen=$IdAlmacen  group by t2.IdLinea,t3.MaterialNo,t3.Material,t3.Shape,t2.Piezas,t1.IdRemision,T5.NombreCliente,T1.IdRemisionEncabezado,t6.NumRecinto;");

// $stmt = $Conexion->prepare($sql);
$productos = $sql->fetchAll(PDO::FETCH_OBJ);

echo json_encode($productos, JSON_UNESCAPED_UNICODE);