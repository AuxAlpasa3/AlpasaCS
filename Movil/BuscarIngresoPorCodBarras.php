<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';


$CodBarras = $_GET['CodBarras'];
$IdAlmacen = $_GET['IdAlmacen'];

if (strpos($CodBarras, ":") !== false) {
    $partes = explode(':', $CodBarras);
    $numero = trim($partes[1]); // "1234"
} else {
    $numero = trim($CodBarras); // "12354"
}

$sentencia = $Conexion->query("SELECT ti.IdTarja as IdRecord, ti.IdLinea, ti.IdRemision, ti.IdArticulo, tin.Piezas, ti.CodBarras, 
ti.FechaIngreso as FechaOperacion, ti.FechaProduccion, ti.IdArticulo, isnull(ti.NumPedido,'N/A') as NumPedido, isnull(ti.NetWeight,0) as NetWeight, 
isnull(ti.GrossWeight,0) as GrossWeight, ti.Origen, ti.Cliente, ti.PaisOrigen, ti.Origen,ti.NoTarima, ti.Cliente, 
ti.Transportista, ti.Placas, ti.Chofer, ti.Checador, ti.Supervisor, ti.Comentarios, 
tu.IdUbicacion,CONCAT(ta.Material,ta.Shape) as MaterialShape, ta.MaterialNo, tu.Ubicacion as Ubicacion, tin.Almacen,ti.Alto,ti.Ancho,ti.Largo,ti.Booking
            FROM t_ingreso ti
            INNER join dbo.t_inventario tin ON tin.CodBarras = ti.CodBarras
            LEFT join t_articulo ta on ta.IdArticulo = ti.IdArticulo
            LEFT join t_ubicacion tu on ti.IdUbicacion = tu.IdUbicacion
            WHERE ti.CodBarras=$numero AND ti.Almacen=$IdAlmacen AND tin.EnProceso=0");

$datos = $sentencia->fetch(PDO::FETCH_OBJ);
if ($datos) {
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
} else {
    echo json_encode(
        array(
            'Mensaje' => 'No se encontró información',
        ),
        JSON_UNESCAPED_UNICODE
    );
}
?>