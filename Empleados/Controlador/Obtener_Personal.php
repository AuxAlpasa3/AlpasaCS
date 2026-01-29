<?php
include '../../api/db/conexion.php';

// Agregar headers para evitar cache
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Incrementar tiempo de ejecución
set_time_limit(60);

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

$noempleado = isset($_POST['noempleado']) ? trim($_POST['noempleado']) : '';
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
$departamento = isset($_POST['departamento']) ? $_POST['departamento'] : '';
$ubicacion = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
$estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
$empresa = isset($_POST['empresa']) ? $_POST['empresa'] : '';
$vehiculo = isset($_POST['vehiculo']) ? $_POST['vehiculo'] : '';

$imagenPorDefecto = 'https://intranet.alpasamx.com/regentsalper/imagenes/empleados/Default.jpg';

$columns = [
    0 => 't1.NoEmpleado',
    1 => null, 
    2 => 't1.Nombre',
    3 => 't1.ApPaterno',
    4 => 't1.ApMaterno',
    5 => 't3.NomCargo',
    6 => 't4.NomDepto',
    7 => 't2.NomEmpresa',
    8 => 't1.Status',
    9 => 't5.NomLargo',
    10 => null,
    11 => null,
    12 => null
];

// Validar columna de orden
$orderColumn = isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== null 
    ? $columns[$orderColumnIndex] 
    : 't1.NoEmpleado';
    
$orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';

try {
    // 1. Contar total de registros (más simple)
    $queryCountTotal = "SELECT COUNT(*) as total FROM t_Personal WHERE NoEmpleado > 0 AND status = 1";
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $resultTotal = $stmtCountTotal->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $resultTotal ? intval($resultTotal['total']) : 0;
    
    // 2. Consulta base
    $queryBase = "SELECT 
        t1.IdPersonal,
        t1.NoEmpleado,
        t1.RutaFoto,
        t1.Nombre,
        t1.ApPaterno,
        t1.ApMaterno,
        t1.Cargo,
        t1.Departamento,
        t1.Empresa,
        t1.Status,
        t1.IdUbicacion,
        ISNULL(t3.NomCargo, 'Sin Cargo') AS NomCargo,
        ISNULL(t4.NomDepto, 'Sin Depto') AS NomDepto,
        ISNULL(t2.NomEmpresa, 'Sin Empresa') AS NomEmpresa,
        CASE 
            WHEN t1.Status = 1 THEN 'Activo'
            WHEN t1.Status = 0 THEN 'Inactivo'
            WHEN t1.Status = 2 THEN 'Baja'
            WHEN t1.Status = 3 THEN 'Vacaciones'
            ELSE 'Desconocido'
        END as StatusTexto,
        ISNULL(t5.NomLargo, 'Sin Ubicacion') as NomCorto 
    FROM t_Personal as t1 
    LEFT JOIN t_empresa as t2 ON t1.Empresa = t2.IdEmpresa 
    LEFT JOIN t_cargo as t3 ON t1.Cargo = t3.IdCargo 
    LEFT JOIN t_departamento as t4 ON t1.Departamento = t4.IdDepartamento 
    LEFT JOIN t_ubicacion as t5 ON t1.IdUbicacion = t5.IdUbicacion
    WHERE t1.NoEmpleado > 0";
    
    // Inicializar array de parámetros
    $params = [];
    $paramTypes = [];
    
    // Inicializar condiciones WHERE
    $whereConditions = ["t1.NoEmpleado > 0"];
    
    // Si hay filtro de estatus, aplicar filtro específico
    if (!empty($estatus)) {
        switch($estatus) {
            case 'Activo':
                $whereConditions[] = "t1.Status = 1";
                break;
            case 'Inactivo':
                $whereConditions[] = "t1.Status = 0";
                break;
            case 'Baja':
                $whereConditions[] = "t1.Status = 2";
                break;
            case 'Vacaciones':
                $whereConditions[] = "t1.Status = 3";
                break;
            default:
                // Si no es ninguno específico, mantener solo activos
                $whereConditions[] = "t1.Status = 1";
                break;
        }
    } else {
        // Por defecto mostrar solo activos
        $whereConditions[] = "t1.Status = 1";
    }
    
    // Filtro de búsqueda general
    if (!empty($searchValue)) {
        $searchParam = "search_" . count($params);
        $whereConditions[] = "(t1.NoEmpleado LIKE :{$searchParam} 
                            OR t1.Nombre LIKE :{$searchParam} 
                            OR t1.ApPaterno LIKE :{$searchParam} 
                            OR t1.ApMaterno LIKE :{$searchParam} 
                            OR t3.NomCargo LIKE :{$searchParam} 
                            OR t4.NomDepto LIKE :{$searchParam} 
                            OR t2.NomEmpresa LIKE :{$searchParam} 
                            OR t5.NomLargo LIKE :{$searchParam})";
        $params[":{$searchParam}"] = "%{$searchValue}%";
        $paramTypes[":{$searchParam}"] = PDO::PARAM_STR;
    }
    
    // Filtros individuales
    if (!empty($noempleado)) {
        $paramName = ":noempleado_" . count($params);
        $whereConditions[] = "t1.NoEmpleado LIKE {$paramName}";
        $params[$paramName] = "%{$noempleado}%";
        $paramTypes[$paramName] = PDO::PARAM_STR;
    }
    
    if (!empty($nombre)) {
        $paramName = ":nombre_" . count($params);
        $whereConditions[] = "(t1.Nombre LIKE {$paramName} 
                            OR t1.ApPaterno LIKE {$paramName} 
                            OR t1.ApMaterno LIKE {$paramName} 
                            OR CONCAT(t1.Nombre, ' ', t1.ApPaterno, ' ', t1.ApMaterno) LIKE {$paramName})";
        $params[$paramName] = "%{$nombre}%";
        $paramTypes[$paramName] = PDO::PARAM_STR;
    }
    
    if (!empty($cargo) && $cargo !== '') {
        $paramName = ":cargo_" . count($params);
        $whereConditions[] = "t1.Cargo = {$paramName}";
        $params[$paramName] = $cargo;
        $paramTypes[$paramName] = PDO::PARAM_INT;
    }
    
    if (!empty($departamento) && $departamento !== '') {
        $paramName = ":departamento_" . count($params);
        $whereConditions[] = "t1.Departamento = {$paramName}";
        $params[$paramName] = $departamento;
        $paramTypes[$paramName] = PDO::PARAM_INT;
    }
    
    if (!empty($ubicacion) && $ubicacion !== '') {
        $paramName = ":ubicacion_" . count($params);
        $whereConditions[] = "t1.IdUbicacion = {$paramName}";
        $params[$paramName] = $ubicacion;
        $paramTypes[$paramName] = PDO::PARAM_INT;
    }
    
    if (!empty($empresa) && $empresa !== '') {
        $paramName = ":empresa_" . count($params);
        $whereConditions[] = "t1.Empresa = {$paramName}";
        $params[$paramName] = $empresa;
        $paramTypes[$paramName] = PDO::PARAM_INT;
    }
    
    // Filtro de vehículos
    if (!empty($vehiculo) && $vehiculo !== '') {
        if ($vehiculo === '1') {
            // Con vehículo
            $whereConditions[] = "EXISTS (SELECT 1 FROM t_vehiculos v WHERE v.NoEmpleado = t1.NoEmpleado AND v.Activo = 1)";
        } elseif ($vehiculo === '0') {
            // Sin vehículo
            $whereConditions[] = "NOT EXISTS (SELECT 1 FROM t_vehiculos v WHERE v.NoEmpleado = t1.NoEmpleado AND v.Activo = 1)";
        }
    }
    
    // Construir consulta con condiciones
    $queryFiltered = $queryBase;
    if (count($whereConditions) > 0) {
        $queryFiltered .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // 3. Contar registros filtrados (consulta simplificada)
    $countQuery = "SELECT COUNT(*) as filtered FROM ({$queryFiltered}) as subquery";
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    
    // Bind de parámetros
    foreach ($params as $param => $value) {
        $type = isset($paramTypes[$param]) ? $paramTypes[$param] : PDO::PARAM_STR;
        $stmtCountFiltered->bindValue($param, $value, $type);
    }
    
    $stmtCountFiltered->execute();
    $filteredResult = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC);
    $filteredRecords = $filteredResult ? intval($filteredResult['filtered']) : 0;
    
    // 4. Consulta final con paginación
    $queryFinal = $queryFiltered . " ORDER BY {$orderColumn} {$orderDirection} 
                  OFFSET :offset ROWS 
                  FETCH NEXT :limit ROWS ONLY";
    
    $stmtFinal = $Conexion->prepare($queryFinal);
    
    // Bind de parámetros de búsqueda
    foreach ($params as $param => $value) {
        $type = isset($paramTypes[$param]) ? $paramTypes[$param] : PDO::PARAM_STR;
        $stmtFinal->bindValue($param, $value, $type);
    }
    
    // Bind de parámetros de paginación
    $stmtFinal->bindValue(':offset', $start, PDO::PARAM_INT);
    $stmtFinal->bindValue(':limit', $length, PDO::PARAM_INT);
    
    $stmtFinal->execute();
    $Personales = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = [];
    
    // Si hay registros, procesarlos
    if ($Personales) {
        foreach($Personales as $Personal) {
            // Consulta de vehículos
            $vehiculosHTML = '';
            try {
                $queryVehiculos = "SELECT * FROM t_vehiculos 
                                  WHERE NoEmpleado = :noempleado AND Activo = 1 
                                  ORDER BY Marca, Modelo";
                $stmtVehiculos = $Conexion->prepare($queryVehiculos);
                $stmtVehiculos->bindValue(':noempleado', $Personal->NoEmpleado, PDO::PARAM_INT);
                $stmtVehiculos->execute();
                $vehiculosData = $stmtVehiculos->fetchAll(PDO::FETCH_OBJ);
                
                if ($vehiculosData && count($vehiculosData) > 0) {
                    $vehiculosHTML = '<div class="dropdown d-inline-block">';
                    $vehiculosHTML .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownVehiculo' . $Personal->NoEmpleado . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $vehiculosHTML .= '<i class="fas fa-car"></i> ' . count($vehiculosData) . ' vehículo(s)';
                    $vehiculosHTML .= '</button>';
                    $vehiculosHTML .= '<div class="dropdown-menu" aria-labelledby="dropdownVehiculo' . $Personal->NoEmpleado . '">';
                    
                    foreach ($vehiculosData as $index => $vehiculo) {
                        $vehiculosHTML .= '<div class="px-3 py-2">';
                        $vehiculosHTML .= '<small>';
                        $vehiculosHTML .= '<strong>' . htmlspecialchars($vehiculo->Marca) . ' ' . htmlspecialchars($vehiculo->Modelo) . '</strong><br>';
                        $vehiculosHTML .= 'Placas: ' . htmlspecialchars($vehiculo->Placas) . '<br>';
                        $vehiculosHTML .= 'Color: ' . htmlspecialchars($vehiculo->Color);
                        
                        if (!empty($vehiculo->RutaFoto)) {
                            $vehiculosHTML .= '<br><a href="#" class="view-vehicle-photo text-primary" data-image="' . htmlspecialchars($vehiculo->RutaFoto) . '" data-info="' . htmlspecialchars($vehiculo->Marca . ' ' . $vehiculo->Modelo . ' - ' . $vehiculo->Placas) . '">Ver foto</a>';
                        }
                        
                        $vehiculosHTML .= '</small>';
                        $vehiculosHTML .= '</div>';
                        
                        if ($index < count($vehiculosData) - 1) {
                            $vehiculosHTML .= '<div class="dropdown-divider"></div>';
                        }
                    }
                    
                    $vehiculosHTML .= '</div></div>';
                } else {
                    $vehiculosHTML = '<span class="badge badge-secondary">Sin vehículo</span>';
                }
            } catch (Exception $e) {
                $vehiculosHTML = '<span class="badge badge-secondary">Error al cargar</span>';
            }
            
            // Determinar estatus
            $badge_class = 'badge-secondary';
            $badge_text = 'Desconocido';
            $estatusBtnClass = 'btn-secondary';
            $estatusBtnText = 'Cambiar';
            
            switch($Personal->Status) {
                case 1:
                    $badge_class = 'badge-success';
                    $badge_text = 'Activo';
                    $estatusBtnClass = 'btn-warning';
                    $estatusBtnText = 'Dar de Baja';
                    break;
                case 0:
                    $badge_class = 'badge-warning';
                    $badge_text = 'Inactivo';
                    $estatusBtnClass = 'btn-success';
                    $estatusBtnText = 'Activar';
                    break;
                case 2:
                    $badge_class = 'badge-danger';
                    $badge_text = 'Baja';
                    $estatusBtnClass = 'btn-success';
                    $estatusBtnText = 'Reactivar';
                    break;
                case 3:
                    $badge_class = 'badge-info';
                    $badge_text = 'Vacaciones';
                    $estatusBtnClass = 'btn-secondary';
                    $estatusBtnText = 'Cambiar';
                    break;
            }
            
            // Foto del empleado
            $fotoHTML = '';
            if(!empty($Personal->RutaFoto) && filter_var($Personal->RutaFoto, FILTER_VALIDATE_URL)) {
                $fotoHTML = '
                    <img src="' . htmlspecialchars($Personal->RutaFoto) . '" 
                         width="50" 
                         height="50" 
                         alt="Foto" 
                         class="img-thumbnail employee-photo"
                         onerror="this.onerror=null; this.src=\'' . $imagenPorDefecto . '\';">
                ';
            } else {
                $fotoHTML = '
                    <img src="' . $imagenPorDefecto . '" 
                         width="50" 
                         height="50" 
                         alt="Sin foto" 
                         class="img-thumbnail employee-photo">
                ';
            }
            
            // Construir fila de datos
            $rowData = [
                'NoEmpleado' => htmlspecialchars($Personal->NoEmpleado),
                'Foto' => $fotoHTML,
                'Nombre' => htmlspecialchars($Personal->Nombre),
                'ApPaterno' => htmlspecialchars($Personal->ApPaterno),
                'ApMaterno' => htmlspecialchars($Personal->ApMaterno),
                'Cargo' => htmlspecialchars($Personal->NomCargo),
                'Departamento' => htmlspecialchars($Personal->NomDepto),
                'Empresa' => htmlspecialchars($Personal->NomEmpresa),
                'Estatus' => '<span class="badge ' . $badge_class . '">' . $badge_text . '</span>',
                'Ubicacion' => htmlspecialchars($Personal->NomCorto),
                'Vehiculo' => $vehiculosHTML,
                'Acceso' => '<a href="GenerarDoc?ID=' . $Personal->IdPersonal . '" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-download"></i></a>',
                'Acciones' => '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-warning btn-editar" data-id="' . $Personal->IdPersonal . '" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn ' . $estatusBtnClass . ' btn-cambiar-estatus" data-id="' . $Personal->IdPersonal . '" title="' . $estatusBtnText . '">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-gestionar-vehiculos" 
                                data-id="' . $Personal->IdPersonal . '"
                                data-noempleado="' . htmlspecialchars($Personal->NoEmpleado) . '"
                                data-nombre="' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno) . '"
                                title="Vehículos">
                            <i class="fas fa-car"></i>
                        </button>
                    </div>
                '
            ];
            
            $data[] = $rowData;
        }
    }
    
    // Retornar respuesta JSON
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    // Registrar error en log
    error_log("Error en consulta de empleados: " . $e->getMessage());
    
    // Retornar error
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en el servidor. Por favor, intente nuevamente."
    ]);
}
?>