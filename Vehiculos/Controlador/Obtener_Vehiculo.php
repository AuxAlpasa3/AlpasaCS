<?php
include_once '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

$imagenPorDefecto = 'https://intranet.alpasamx.com/AlpasaCS/Vehiculos/vehiculos/Default.jpg';

// Recibir filtros adicionales
$noempleado = $_POST['noempleado'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$placas = $_POST['placas'] ?? '';
$tipovehiculo = $_POST['tipovehiculo'] ?? '';
$libreuso = $_POST['libreuso'] ?? '';
$estatus = $_POST['estatus'] ?? '';

$columns = [
    0 => 't1.IdVehiculo',
    1 => null, // Foto - no ordenable
    2 => 't1.Marca',
    3 => 't1.Modelo',
    4 => 't1.Num_Serie',
    5 => 't1.Placas',
    6 => 't1.Anio',
    7 => 't1.Color',
    8 => 't1.Activo',
    9 => 't1.LibreUso',
    10 => 't1.TipoVehiculo',
    11 => null, // Personal asignado - no ordenable
    12 => null  // Acciones - no ordenable
];

$orderColumn = $columns[$orderColumnIndex] ?? 't1.IdVehiculo';
$orderDirection = ($orderDirection == 'desc') ? 'DESC' : 'ASC';

try {
    // Conteo total de vehículos
    $queryCountTotal = "SELECT COUNT(*) as total FROM t_vehiculos as t1";
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Query base con JOIN a t_personal para obtener el empleado asignado
    $queryBase = "SELECT 
                    t1.IdVehiculo,
                    t1.RutaFoto,
                    t1.Marca,
                    t1.Modelo,
                    t1.Num_Serie,
                    t1.Placas,
                    t1.Anio,
                    t1.Color,
                    t1.Activo,
                    t1.LibreUso,
                    t1.TipoVehiculo,
                    t1.IdAsociado,
                    t2.NoEmpleado,
                    t2.Nombre as NombreEmpleado,
                    t2.ApPaterno as ApPaternoEmpleado,
                    t2.ApMaterno as ApMaternoEmpleado,
                    t2.Status as EstatusEmpleado,
                    CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as EmpleadoAsociado,
                    CASE 
                        WHEN t1.LibreUso = 0 THEN 'Privado' 
                        WHEN t1.LibreUso = 1 THEN 'Laboral' 
                        ELSE 'No definido'
                    END as LibreUsoTexto,
                    CASE 
                        WHEN t1.TipoVehiculo = 1 THEN 'Personal'
                        WHEN t1.TipoVehiculo = 2 THEN 'Externo' 
                        WHEN t1.TipoVehiculo = 3 THEN 'Proveedor' 
                        WHEN t1.TipoVehiculo = 4 THEN 'Visitas'
                        ELSE 'No definido'
                    END as TipoVehiculoTexto,
                    CASE 
                        WHEN t1.Activo = 1 THEN 'Activo'
                        WHEN t1.Activo = 0 THEN 'Inactivo'
                        ELSE 'No definido'
                    END as EstatusTexto
                FROM t_vehiculos as t1 
                LEFT JOIN t_personal as t2 ON t1.IdAsociado = t2.NoEmpleado
                WHERE 1=1";
    
    $queryFiltered = $queryBase;
    $searchParams = [];
    $paramCount = 0;
    
    // Búsqueda general
    if (!empty($searchValue)) {
        $paramName = ":search_" . $paramCount++;
        $queryFiltered .= " AND (t1.Placas LIKE $paramName 
                            OR t1.Marca LIKE $paramName 
                            OR t1.Modelo LIKE $paramName 
                            OR t1.Num_Serie LIKE $paramName
                            OR t1.Color LIKE $paramName
                            OR t2.Nombre LIKE $paramName 
                            OR t2.ApPaterno LIKE $paramName 
                            OR t2.ApMaterno LIKE $paramName)";
        $searchParams[$paramName] = "%{$searchValue}%";
    }
    
    // Filtros específicos
    if (!empty($noempleado)) {
        $paramName = ":noempleado_" . $paramCount++;
        $queryFiltered .= " AND t2.NoEmpleado LIKE $paramName";
        $searchParams[$paramName] = "%{$noempleado}%";
    }
    
    if (!empty($nombre)) {
        $paramName = ":nombre_" . $paramCount++;
        $paramName2 = ":nombre2_" . $paramCount++;
        $paramName3 = ":nombre3_" . $paramCount++;
        $paramName4 = ":nombre4_" . $paramCount++;
        
        $queryFiltered .= " AND (t2.Nombre LIKE $paramName 
                            OR t2.ApPaterno LIKE $paramName2 
                            OR t2.ApMaterno LIKE $paramName3 
                            OR CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) LIKE $paramName4)";
        
        $nombreTerm = "%{$nombre}%";
        $searchParams[$paramName] = $nombreTerm;
        $searchParams[$paramName2] = $nombreTerm;
        $searchParams[$paramName3] = $nombreTerm;
        $searchParams[$paramName4] = $nombreTerm;
    }
    
    if (!empty($placas)) {
        $paramName = ":placas_" . $paramCount++;
        $queryFiltered .= " AND t1.Placas LIKE $paramName";
        $searchParams[$paramName] = "%{$placas}%";
    }
    
    if (!empty($tipovehiculo)) {
        $paramName = ":tipovehiculo_" . $paramCount++;
        $queryFiltered .= " AND t1.TipoVehiculo = $paramName";
        $searchParams[$paramName] = $tipovehiculo;
    }
    
    if (!empty($libreuso)) {
        $paramName = ":libreuso_" . $paramCount++;
        $queryFiltered .= " AND t1.LibreUso = $paramName";
        $searchParams[$paramName] = $libreuso;
    }
    
    if (!empty($estatus) && $estatus !== '') {
        if ($estatus === 'Activo') {
            $queryFiltered .= " AND t1.Activo = 1";
        } elseif ($estatus === 'Inactivo') {
            $queryFiltered .= " AND t1.Activo = 0";
        }
    }
    
    // Conteo de registros filtrados
    $countQuery = "SELECT COUNT(*) as filtered FROM ($queryFiltered) as filtered_table";
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    
    foreach ($searchParams as $param => $value) {
        $stmtCountFiltered->bindValue($param, $value);
    }
    
    $stmtCountFiltered->execute();
    $filteredRecords = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC)['filtered'];
    
    // Query final con paginación
    $queryFinal = $queryFiltered . " ORDER BY {$orderColumn} {$orderDirection} 
                  OFFSET :start ROWS 
                  FETCH NEXT :length ROWS ONLY";
    
    $stmtFinal = $Conexion->prepare($queryFinal);
    
    foreach ($searchParams as $param => $value) {
        $stmtFinal->bindValue($param, $value);
    }
    
    $stmtFinal->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmtFinal->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
    $stmtFinal->execute();
    $Vehiculos = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = [];
    foreach($Vehiculos as $vehiculo) {
        // Determinar la foto del vehículo
        $foto = !empty($vehiculo->RutaFoto) ? $vehiculo->RutaFoto : $imagenPorDefecto;
        
        // Badge para el estatus del vehículo
        $badgeClass = $vehiculo->Activo == 1 ? 'badge-success' : 'badge-danger';
        
        // Badge para libre uso
        $libreUsoBadge = $vehiculo->LibreUso == 1 ? 'badge-info' : 'badge-secondary';
        
        // Badge para tipo de vehículo
        $tipoVehiculoBadge = 'badge-primary';
        
        // Información del empleado asignado
        $empleadoAsignado = '';
        if (!empty($vehiculo->EmpleadoAsociado)) {
            $empleadoAsignado = '<div class="employee-info">';
            $empleadoAsignado .= '<span class="badge badge-success" style="cursor: pointer;" 
                                    onclick="mostrarInformacionEmpleado(' . $vehiculo->IdAsociado . ', \'' . htmlspecialchars($vehiculo->EmpleadoAsociado) . '\')">';
            $empleadoAsignado .= '<i class="fas fa-user mr-1"></i>' . htmlspecialchars($vehiculo->EmpleadoAsociado);
            $empleadoAsignado .= '</span>';
            $empleadoAsignado .= '<small class="text-muted d-block">No. ' . htmlspecialchars($vehiculo->NoEmpleado) . '</small>';
            $empleadoAsignado .= '</div>';
        } else {
            $empleadoAsignado = '<span class="badge badge-secondary">No asignado</span>';
        }
        
        // Acciones
        $accionesHTML = '<div class="btn-group" role="group">';
        $accionesHTML .= '<button type="button" class="btn btn-sm btn-primary btn-editar" 
                            data-idvehiculo="' . htmlspecialchars($vehiculo->IdVehiculo) . '"
                            title="Editar vehículo">
                            <i class="fas fa-edit"></i>
                         </button>';
        
        if ($vehiculo->Activo == 1) {
            $accionesHTML .= '<button type="button" class="btn btn-sm btn-danger btn-cambiar-estatus" 
                                data-idvehiculo="' . htmlspecialchars($vehiculo->IdVehiculo) . '"
                                data-activo="' . $vehiculo->Activo . '"
                                data-placas="' . htmlspecialchars($vehiculo->Placas) . '"
                                title="Desactivar vehículo">
                                <i class="fas fa-toggle-off"></i>
                             </button>';
        } else {
            $accionesHTML .= '<button type="button" class="btn btn-sm btn-success btn-cambiar-estatus" 
                                data-idvehiculo="' . htmlspecialchars($vehiculo->IdVehiculo) . '"
                                data-activo="' . $vehiculo->Activo . '"
                                data-placas="' . htmlspecialchars($vehiculo->Placas) . '"
                                title="Activar vehículo">
                                <i class="fas fa-toggle-on"></i>
                             </button>';
        }
        
        $accionesHTML .= '<button type="button" class="btn btn-sm btn-info btn-asignar" 
                            data-idvehiculo="' . htmlspecialchars($vehiculo->IdVehiculo) . '"
                            data-placas="' . htmlspecialchars($vehiculo->Placas) . '"
                            title="Asignar/Reasignar vehículo">
                            <i class="fas fa-user-tie"></i>
                         </button>';
        $accionesHTML .= '</div>';
        
        $rowData = [
            'IdVehiculo' => htmlspecialchars($vehiculo->IdVehiculo),
            'Foto' => $foto,
            'Marca' => htmlspecialchars($vehiculo->Marca),
            'Modelo' => htmlspecialchars($vehiculo->Modelo),
            'NumSerie' => htmlspecialchars($vehiculo->Num_Serie),
            'Placas' => htmlspecialchars($vehiculo->Placas),
            'Anio' => htmlspecialchars($vehiculo->Anio),
            'Color' => htmlspecialchars($vehiculo->Color),
            'Estatus' => '<span class="badge ' . $badgeClass . '">' . $vehiculo->EstatusTexto . '</span>',
            'LibreUso' => '<span class="badge ' . $libreUsoBadge . '">' . $vehiculo->LibreUsoTexto . '</span>',
            'TipoVehiculo' => '<span class="badge ' . $tipoVehiculoBadge . '">' . $vehiculo->TipoVehiculoTexto . '</span>',
            'AsignadoA' => $empleadoAsignado,
            'Acciones' => $accionesHTML,
            'Activo' => $vehiculo->Activo,
            'IdAsociado' => $vehiculo->IdAsociado,
            'EmpleadoAsociado' => $vehiculo->EmpleadoAsociado
        ];
        
        $data[] = $rowData;
    }
    
    $response = [
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $data,
        "error" => null
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    $response = [
        "draw" => intval($draw),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en la consulta: " . $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>