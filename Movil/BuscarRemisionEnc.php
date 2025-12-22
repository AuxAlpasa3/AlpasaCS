<?php
header('Content-Type: text/html; charset=UTF-8');
include '../api/db/conexion.php';


$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'];
$Almacen = $_GET['Almacen'];

try {
    $sentencia = $Conexion->prepare("SELECT 
    Distinct(t1.IdRemisionEncabezado),
        t1.IdRemision,
        t2.NombreCliente, 
        t1.Transportista,
        t1.Placas, 
        t1.Chofer, 
        t1.Almacen,
		t1.Contenedor,
		t1.Sellos,
		t1.Tracto,
		t1.Caja
    FROM t_remision_encabezado AS t1 
    INNER JOIN t_cliente AS t2 ON t1.Cliente = t2.IdCliente 
    WHERE  t1.IdRemisionEncabezado = :IdRemisionEncabezado AND t1.Almacen= :Almacen");

    $sentencia->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezado, PDO::PARAM_STR);
    $sentencia->bindParam(':Almacen', $Almacen, PDO::PARAM_INT);
    $sentencia->execute();

    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if ($Query) {
        $datos = array();
        foreach ($Query as $row) {
            array_push($datos, array(
                
                'IdRemisionEncabezado' => $row->IdRemisionEncabezado,
                'IdRemision' => $row->IdRemision,
                'Cliente' => $row->NombreCliente,
                'Transportista' => $row->Transportista,
                'Placas' => $row->Placas,
                'Chofer' => $row->Chofer,
                'Almacen' => $row->Almacen,
                'Contenedor' => $row->Contenedor,
                'Sellos' => $row->Sellos,
                'Tracto' => $row->Tracto,
                'Caja' => $row->Caja
            ));
        }
    } else {
        $datos = ['error' => 'La Remisión no está disponible, favor de validar con el Administrador'];
    }

    echo json_encode($datos, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>