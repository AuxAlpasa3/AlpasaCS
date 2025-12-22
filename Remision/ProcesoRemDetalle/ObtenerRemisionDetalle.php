<?php
include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

ob_clean();

header('Content-Type: application/json');

try {
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 25;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

    $IdRemision = isset($_POST['IdRemision']) ? $_POST['IdRemision'] : '';
    $IdRemisionEncabezado = isset($_POST['IdRemisionEncabezado']) ? $_POST['IdRemisionEncabezado'] : '';
    $IdAlmacen = isset($_POST['IdAlmacen']) ? $_POST['IdAlmacen'] : '';

    if (empty($IdRemision) || empty($IdRemisionEncabezado)) {
        throw new Exception("Parámetros requeridos faltantes");
    }

    $query = "
        SELECT t1.IdRemision, t1.IdRemisionEncabezado, t2.IdLinea, 
               t2.IdArticulo as IdArticuloNum, t3.MaterialNo, 
               CONCAT(t3.Material, ' ', COALESCE(t3.Shape, '')) as Articulo, 
               t2.Piezas, t1.Cliente, t2.Booking
        FROM t_remision_encabezado AS t1 
        INNER JOIN t_remision_linea AS t2 ON t1.IdRemision = t2.IdRemision
        INNER JOIN t_articulo AS t3 ON t2.IdArticulo = t3.IdArticulo
        INNER JOIN t_usuario_almacen AS t8 ON t1.Almacen = t8.IdAlmacen
        WHERE t1.Estatus IN (0,1) 
        AND t8.IdUsuario = ? 
        AND t1.IdRemisionEncabezado = ? 
        AND t1.IdRemision = ?
    ";

    $params = [$IdUsuario, $IdRemisionEncabezado, $IdRemision];

    if (!empty($search)) {
        $query .= " AND (t3.MaterialNo LIKE ? OR CONCAT(t3.Material, ' ', COALESCE(t3.Shape, '')) LIKE ? OR t2.Piezas LIKE ? OR t2.Booking LIKE ?)";
        $searchParam = "%$search%";
        array_push($params, $searchParam, $searchParam, $searchParam, $searchParam);
    }

    $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    $columns = ['t2.IdLinea', 't3.MaterialNo', 'Articulo', 't2.Piezas', 't2.Booking', 't2.IdLinea'];

    if (isset($columns[$orderColumnIndex])) {
        $query .= " ORDER BY " . $columns[$orderColumnIndex] . " " . $orderDirection;
    } else {
        $query .= " ORDER BY t2.IdLinea ASC";
    }

    // Agregar LIMIT para paginación
    
    $params[] = $length;
    $params[] = $start;

    $stmt = $Conexion->prepare($query);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $queryTotal = "
        SELECT COUNT(*) as total
        FROM t_remision_encabezado AS t1 
        INNER JOIN t_remision_linea AS t2 ON t1.IdRemision = t2.IdRemision
        INNER JOIN t_articulo AS t3 ON t2.IdArticulo = t3.IdArticulo
        INNER JOIN t_usuario_almacen AS t8 ON t1.Almacen = t8.IdAlmacen
        WHERE t1.Estatus IN (0,1) 
        AND t8.IdUsuario = ? 
        AND t1.IdRemisionEncabezado = ? 
        AND t1.IdRemision = ?
    ";

    $stmtTotal = $Conexion->prepare($queryTotal);
    $stmtTotal->execute([$IdUsuario, $IdRemisionEncabezado, $IdRemision]);
    $totalRegistros = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    if (!empty($search)) {
        $queryFiltrado = $queryTotal . " AND (t3.MaterialNo LIKE ? OR CONCAT(t3.Material, ' ', COALESCE(t3.Shape, '')) LIKE ? OR t2.Piezas LIKE ? OR t2.Booking LIKE ?)";
        $stmtFiltrado = $Conexion->prepare($queryFiltrado);
        $stmtFiltrado->execute([$IdUsuario, $IdRemisionEncabezado, $IdRemision, "%$search%", "%$search%", "%$search%", "%$search%"]);
        $totalFiltrado = $stmtFiltrado->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $totalFiltrado = $totalRegistros;
    }

    $response = [
        "draw" => $draw,
        "recordsTotal" => intval($totalRegistros),
        "recordsFiltered" => intval($totalFiltrado),
        "data" => $resultados
    ];

    echo json_encode($response);

} catch (Exception $e) {
    $response = [
        "draw" => isset($draw) ? $draw : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en la consulta: " . $e->getMessage()
    ];
    echo json_encode($response);
}
?>