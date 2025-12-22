<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

$IdAlmacen = isset($_GET['IdAlmacen']) ? $_GET['IdAlmacen'] : null;

if (!$IdAlmacen || !is_numeric($IdAlmacen)) {
    echo json_encode(["error" => "IdAlmacen inválido o vacío: " . $IdAlmacen], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $query = "WITH RemisionesUnicas AS (
    SELECT 
        t2.IdTarja,
        t3.NombreCliente,
        t5.NumRecinto,
        t5.IdAlmacen,
        t1.Transportista,
        t1.Placas,
        t1.Chofer,
        t1.Contenedor,
        t1.Caja,
        t1.Tracto,
        t1.Sellos,
        t1.IdRemision
    FROM t_remision_encabezado as t1 
    INNER JOIN t_ingreso as t2 on t1.IdRemisionEncabezado = t2.IdRemision
    LEFT JOIN t_cliente t3 ON t3.IdCliente = t2.Cliente
    LEFT JOIN t_almacen t5 ON t5.IdAlmacen = t2.Almacen
    WHERE t2.Estatus IN (0,1,2) AND t1.Almacen = $IdAlmacen
    GROUP BY 
        t2.IdTarja,
        t3.NombreCliente,
        t5.NumRecinto,
        t5.IdAlmacen,
        t1.Transportista,
        t1.Placas,
        t1.Chofer,
        t1.Contenedor,
        t1.Caja,
        t1.Tracto,
        t1.Sellos,
        t1.IdRemision
)
SELECT 
    IdTarja, 
    NombreCliente,
    NumRecinto,
    STRING_AGG(IdRemision, ', ') as IdRemision,
    IdAlmacen,
    Transportista,
    Placas,
    Chofer,
    Contenedor,
    Caja,
    Tracto,
    Sellos
FROM RemisionesUnicas
GROUP BY 
    IdTarja,
    NombreCliente,
    NumRecinto,
    IdAlmacen,
    Transportista,
    Placas,
    Chofer,
    Contenedor,
    Caja,
    Tracto,
    Sellos";
    
    $sentencia = $Conexion->query($query);
    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if ($Query && count($Query) > 0) {
        $datos = [];
        foreach ($Query as $row) {
            $datos[] = array(
                'IdTarja' => $row->IdTarja,
                'IdRemision' => $row->IdRemision,
                'Cliente' => $row->NombreCliente,
                'NumRecinto' => $row->NumRecinto,
                'IdAlmacen' => $row->IdAlmacen,
                'Transportista' => $row->Transportista,
                'Placas' => $row->Placas,
                'Chofer' => $row->Chofer,
                'Contenedor' => $row->Contenedor,
                'Caja' => $row->Caja,
                'Tracto' => $row->Tracto,
                'Sellos' => $row->Sellos
            );
        }
        echo json_encode([
            "success" => true,
            "data" => $datos
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No hay ingresos pendientes."
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}