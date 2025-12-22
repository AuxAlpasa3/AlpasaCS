<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

set_time_limit(30);
ini_set('memory_limit', '256M');

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    require_once "../templates/sesionP.php";
    
    if (!isset($Conexion) || !$Conexion) {
        throw new Exception("No se pudo establecer conexión con la base de datos");
    }

    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 25;
    $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    if ($start < 0) $start = 0;
    if ($length < 1) $length = 25;
    if ($length > 100) $length = 100; 

    $columns = [
        0 => 't1.IdRemision',
        1 => 't4.NombreCliente',
        2 => 't1.Transportista',
        3 => 't1.Contenedor',
        4 => 't1.FechaRemision',
        5 => 't6.TipoRemision',
        6 => 't5.Estatus'
    ];

    $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 't1.IdRemision';
    $orderDir = in_array(strtolower($orderDirection), ['asc', 'desc']) ? $orderDirection : 'asc';

    $params = [];
    $whereConditions = ["t1.Estatus IN (0,1)"];

    // Búsqueda
    if (!empty($search)) {
        $whereConditions[] = "(t1.IdRemision LIKE ? OR t4.NombreCliente LIKE ? OR t1.Transportista LIKE ? OR t1.Placas LIKE ? OR t1.Chofer LIKE ? OR t6.TipoRemision LIKE ? OR t5.Estatus LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, array_fill(0, 7, $searchParam));
    }

    $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";

    // Consulta para el total de registros
    $sqlCount = "SELECT COUNT(*) as total
                 FROM t_remision_encabezado AS t1 
                 INNER JOIN t_estatusrem AS t5 ON t1.Estatus = t5.idEstatus 
                 INNER JOIN t_tipoRemision AS t6 ON t1.TipoRemision = t6.IdTipoRemision
                 INNER JOIN t_cliente AS t4 ON t1.Cliente = t4.IdCliente 
                 $whereClause";

    try {
        $sentCount = $Conexion->prepare($sqlCount);
        $sentCount->execute($params);
        $resultCount = $sentCount->fetch(PDO::FETCH_OBJ);
        $totalRecords = $resultCount ? $resultCount->total : 0;
    } catch (PDOException $e) {
        throw new Exception("Error en consulta de conteo: " . $e->getMessage());
    }

    $filteredRecords = $totalRecords;

    $sqlData = "SELECT 
                t1.IdRemision,
                t1.IdRemisionEncabezado,
                t1.Transportista,
                t1.Placas,
                t1.Chofer,
                t1.FechaRemision,
                t1.TipoRemision as TipoRemisionNum,
                t6.TipoRemision,
                t1.Cantidad,
                t1.FechaRegistro,
                t1.Estatus as EstatusNum,
                t5.Estatus,
                t1.Contenedor,
                t1.Sellos,
                t1.Caja,
                t1.Tracto,
                t1.Almacen as IdAlmacen,
                t4.NombreCliente as Cliente,
                t4.IdCliente
            FROM t_remision_encabezado AS t1 
            INNER JOIN t_estatusrem AS t5 ON t1.Estatus = t5.idEstatus 
            INNER JOIN t_tipoRemision AS t6 ON t1.TipoRemision = t6.IdTipoRemision
            INNER JOIN t_cliente AS t4 ON t1.Cliente = t4.IdCliente
            $whereClause
            ORDER BY $orderBy $orderDir
            OFFSET $start ROWS
            FETCH NEXT $length ROWS ONLY";

    // Agregar parámetros de paginación
    $paginationParams = array_merge($params, [$start, $length]);

    try {
        $sentData = $Conexion->prepare($sqlData);
        $sentData->execute($paginationParams);
        $remisiones = $sentData->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        throw new Exception("Error en consulta de datos: " . $e->getMessage());
    }

    $data = [];
    
    foreach ($remisiones as $remision) {
        // Verificar líneas sin piezas
        $totalLineasSinPiezas = 0;
        try {
            $sqlLineas = "SELECT COUNT(*) AS TotalLineasSinPiezas
                         FROM t_remision_linea
                         WHERE IdRemision = ?
                         AND ISNULL(Piezas, 0) = 0";
            $sentLineas = $Conexion->prepare($sqlLineas);
            $sentLineas->execute([$remision->IdRemision]);
            $resultLineas = $sentLineas->fetch(PDO::FETCH_OBJ);
            $totalLineasSinPiezas = $resultLineas ? $resultLineas->TotalLineasSinPiezas : 0;
        } catch (PDOException $e) {
            error_log("Error contando líneas sin piezas: " . $e->getMessage());
        }

        // Determinar clase del badge según estatus
        $badgeClass = 'badge-secondary';
        switch ($remision->EstatusNum) {
            case 0:
                $badgeClass = 'badge-warning';
                break;
            case 1:
                $badgeClass = 'badge-info';
                break;
            case 2:
                $badgeClass = 'badge-success';
                break;
            case 3:
                $badgeClass = 'badge-danger';
                break;
        }

        // Preparar datos para JSON
        $data[] = [
            'IdRemision' => $remision->IdRemision ?? '',
            'IdRemisionEncabezado' => $remision->IdRemisionEncabezado ?? '',
            'Cliente' => $remision->Cliente ?? '',
            'Transportista' => $remision->Transportista ?? '',
            'Placas' => $remision->Placas ?? '',
            'Chofer' => $remision->Chofer ?? '',
            'FechaRemision' => $remision->FechaRemision ?? '',
            'TipoRemisionNum' => $remision->TipoRemisionNum ?? 0,
            'TipoRemision' => $remision->TipoRemision ?? '',
            'EstatusNum' => $remision->EstatusNum ?? 0,
            'Estatus' => $remision->Estatus ?? '',
            'Contenedor' => $remision->Contenedor ?? '',
            'Sellos' => $remision->Sellos ?? '',
            'Caja' => $remision->Caja ?? '',
            'Tracto' => $remision->Tracto ?? '',
            'IdAlmacen' => $remision->IdAlmacen ?? 0,
            'TotalLineasSinPiezas' => $totalLineasSinPiezas,
            'BadgeClass' => $badgeClass,
            'Disabled' => (empty($remision->Transportista) || empty($remision->Placas) || empty($remision->Chofer) || $totalLineasSinPiezas != 0) ? 'disabled' : '',
            'Title' => (empty($remision->Transportista) || empty($remision->Placas) || empty($remision->Chofer) || $totalLineasSinPiezas != 0) ? 
                'Complete los datos de Transportista, Placas, Chofer y Número de Piezas para habilitar el botón' : ''
        ];
    }

    // Respuesta exitosa
    sendJsonResponse([
        'draw' => $draw,
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($filteredRecords),
        'data' => $data
    ]);

} catch (PDOException $e) {
    error_log("Error PDO en Obtener_RemisionesServerSide: " . $e->getMessage());
    sendJsonResponse([
        'draw' => intval($draw ?? 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Error de base de datos'
    ], 500);
} catch (Exception $e) {
    error_log("Error general en Obtener_RemisionesServerSide: " . $e->getMessage());
    sendJsonResponse([
        'draw' => intval($draw ?? 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Error del sistema'
    ], 500);
}
?>