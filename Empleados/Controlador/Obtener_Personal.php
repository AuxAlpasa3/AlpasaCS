<?php
include_once '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

$noempleado = $_POST['noempleado'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$departamento = $_POST['departamento'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$estatus = $_POST['estatus'] ?? '';
$empresa = $_POST['empresa'] ?? '';
$vehiculo = $_POST['vehiculo'] ?? '';

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
    11 => null  
];

$orderColumn = $columns[$orderColumnIndex] ?? 't1.NoEmpleado';
$orderDirection = ($orderDirection == 'desc') ? 'DESC' : 'ASC';

try {
    $queryCountTotal = "SELECT COUNT(*) as total FROM t_Personal as t1 WHERE NoEmpleado > 0";
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
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
                    (CASE 
                        WHEN t1.Cargo = 0 THEN 'Sin Cargo' 
                        ELSE t3.NomCargo 
                     END) AS CargoNombre, 
                    (CASE 
                        WHEN t1.Departamento = 0 THEN 'Sin Departamento' 
                        ELSE t4.NomDepto 
                     END) AS DepartamentoNombre,
                    (CASE 
                        WHEN t1.Empresa = 0 THEN 'Sin Empresa' 
                        ELSE t2.NomEmpresa 
                     END) AS EmpresaNombre,
                    (CASE 
                        WHEN t1.Status = 1 THEN 'Activo' 
                        WHEN t1.Status = 0 THEN 'Inactivo' 
                        WHEN t1.Status = 2 THEN 'Baja' 
                        WHEN t1.Status = 3 THEN 'Vacaciones'
                        ELSE 'Desconocido'
                     END) as EstatusTexto,
                    (CASE 
                        WHEN t1.IdUbicacion = 0 THEN 'Sin Ubicación' 
                        ELSE t5.NomLargo 
                     END) as UbicacionNombre 
                  FROM t_Personal as t1 
                  LEFT JOIN t_empresa as t2 ON t1.Empresa = t2.IdEmpresa 
                  LEFT JOIN t_cargo as t3 ON t1.Cargo = t3.IdCargo 
                  LEFT JOIN t_departamento as t4 ON t4.IdDepartamento = t1.Departamento 
                  LEFT JOIN t_ubicacion as t5 ON t5.IdUbicacion = t1.IdUbicacion
                  WHERE t1.NoEmpleado > 0";
    
    $queryFiltered = $queryBase;
    $searchParams = [];
    $paramCount = 0;
    
    if (!empty($searchValue)) {
        $paramName = ":search_" . $paramCount++;
        $queryFiltered .= " AND (t1.NoEmpleado LIKE $paramName 
                            OR t1.Nombre LIKE $paramName 
                            OR t1.ApPaterno LIKE $paramName 
                            OR t1.ApMaterno LIKE $paramName 
                            OR t3.NomCargo LIKE $paramName 
                            OR t4.NomDepto LIKE $paramName 
                            OR t2.NomEmpresa LIKE $paramName 
                            OR t5.NomLargo LIKE $paramName)";
        $searchParams[$paramName] = "%{$searchValue}%";
    }
    
    if (!empty($noempleado)) {
        $paramName = ":noempleado_" . $paramCount++;
        $queryFiltered .= " AND t1.NoEmpleado LIKE $paramName";
        $searchParams[$paramName] = "%{$noempleado}%";
    }
    
    if (!empty($nombre)) {
        $paramName = ":nombre_" . $paramCount++;
        $paramName2 = ":nombre2_" . $paramCount++;
        $paramName3 = ":nombre3_" . $paramCount++;
        $paramName4 = ":nombre4_" . $paramCount++;
        
        $queryFiltered .= " AND (t1.Nombre LIKE $paramName 
                            OR t1.ApPaterno LIKE $paramName2 
                            OR t1.ApMaterno LIKE $paramName3 
                            OR CONCAT(t1.Nombre, ' ', t1.ApPaterno, ' ', t1.ApMaterno) LIKE $paramName4)";
        
        $nombreTerm = "%{$nombre}%";
        $searchParams[$paramName] = $nombreTerm;
        $searchParams[$paramName2] = $nombreTerm;
        $searchParams[$paramName3] = $nombreTerm;
        $searchParams[$paramName4] = $nombreTerm;
    }
    
    if (!empty($cargo) && $cargo !== '') {
        $paramName = ":cargo_" . $paramCount++;
        $queryFiltered .= " AND t1.Cargo = $paramName";
        $searchParams[$paramName] = $cargo;
    }
    
    if (!empty($departamento) && $departamento !== '') {
        $paramName = ":departamento_" . $paramCount++;
        $queryFiltered .= " AND t1.Departamento = $paramName";
        $searchParams[$paramName] = $departamento;
    }
    
    if (!empty($ubicacion) && $ubicacion !== '') {
        $paramName = ":ubicacion_" . $paramCount++;
        $queryFiltered .= " AND t1.IdUbicacion = $paramName";
        $searchParams[$paramName] = $ubicacion;
    }
    
    if (!empty($estatus) && $estatus !== '') {
        if ($estatus === 'Activo') {
            $queryFiltered .= " AND t1.Status = 1";
        } elseif ($estatus === 'Inactivo') {
            $queryFiltered .= " AND t1.Status = 0";
        } elseif ($estatus === 'Baja') {
            $queryFiltered .= " AND t1.Status = 2";
        }
    }
    
    if (!empty($empresa) && $empresa !== '') {
        $paramName = ":empresa_" . $paramCount++;
        $queryFiltered .= " AND t1.Empresa = $paramName";
        $searchParams[$paramName] = $empresa;
    }
    
    if (!empty($vehiculo) && $vehiculo !== '') {
        if ($vehiculo === '1') {
            $queryFiltered .= " AND EXISTS (
                SELECT 1 FROM t_vehiculos v 
                WHERE v.IdAsociado = t1.NoEmpleado AND v.Activo = 1
            )";
        } elseif ($vehiculo === '0') {
            $queryFiltered .= " AND NOT EXISTS (
                SELECT 1 FROM t_vehiculos v 
                WHERE v.IdAsociado = t1.NoEmpleado AND v.Activo = 1
            )";
        }
    }
    
    $countQuery = "SELECT COUNT(*) as filtered FROM ($queryFiltered) as filtered_table";
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    
    foreach ($searchParams as $param => $value) {
        $stmtCountFiltered->bindValue($param, $value);
    }
    
    $stmtCountFiltered->execute();
    $filteredRecords = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC)['filtered'];
    
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
    $Personales = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = [];
    foreach($Personales as $Personal) {
        $queryVehiculo = "SELECT COUNT(*) as tieneVehiculo 
                          FROM t_vehiculos 
                          WHERE IdAsociado = :noempleado 
                          AND Activo = 1";
        
        $stmtVehiculo = $Conexion->prepare($queryVehiculo);
        $stmtVehiculo->bindValue(':noempleado', $Personal->NoEmpleado);
        $stmtVehiculo->execute();
        $tieneVehiculo = $stmtVehiculo->fetch(PDO::FETCH_ASSOC)['tieneVehiculo'] > 0;
        
        $badgeClass = '';
        switch($Personal->Status) {
            case 1: $badgeClass = 'badge-success'; break;  // Activo
            case 0: $badgeClass = 'badge-danger'; break;   // Inactivo
            case 2: $badgeClass = 'badge-dark'; break;     // Baja
            default: $badgeClass = 'badge-secondary';
        }
        
        $foto = !empty($Personal->RutaFoto) ? $Personal->RutaFoto : $imagenPorDefecto;
        $nombreCompleto = trim($Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno);
        
        $fotoHTML = '';
        if (!empty($Personal->RutaFoto) && $Personal->RutaFoto !== $imagenPorDefecto) {
            $fotoHTML = $foto;
        } else {
            $fotoHTML = $imagenPorDefecto;
        }
        
        $vehiculoHTML = '';
        if ($tieneVehiculo) {
            $vehiculoHTML = '<span class="badge badge-primary vehicle-badge btn-ver-vehiculo" 
                                 style="cursor: pointer;"
                                 data-noempleado="' . htmlspecialchars($Personal->NoEmpleado) . '" 
                                 data-nombre="' . htmlspecialchars($nombreCompleto) . '">
                                 Con Vehículo
                              </span>';
        } else {
            $vehiculoHTML = '<span class="badge badge-secondary">Sin Vehículo</span>';
        }
        
        $accesoHTML = '<form action="Controlador/Credencial.php" method="POST" target="_blank" style="display: inline;">
                          <input type="hidden" name="IdPersonal" value="' . htmlspecialchars($Personal->IdPersonal) . '">
                          <button type="submit" class="btn btn-sm btn-outline-primary">
                              <i class="fas fa-id-card"></i> Credencial
                          </button>
                      </form>';
        
        $rowData = [
            'IdPersonal' => htmlspecialchars($Personal->IdPersonal),
            'NoEmpleado' => htmlspecialchars($Personal->NoEmpleado),
            'Foto' => $fotoHTML,
            'Nombre' => htmlspecialchars($Personal->Nombre),
            'ApPaterno' => htmlspecialchars($Personal->ApPaterno),
            'ApMaterno' => htmlspecialchars($Personal->ApMaterno),
            'Cargo' => htmlspecialchars($Personal->CargoNombre),
            'Departamento' => htmlspecialchars($Personal->DepartamentoNombre),
            'Empresa' => htmlspecialchars($Personal->EmpresaNombre),
            'Estatus' => htmlspecialchars($Personal->EstatusTexto),
            'Ubicacion' => htmlspecialchars($Personal->UbicacionNombre),
            'Vehiculo' => $vehiculoHTML,
            'Acceso' => $accesoHTML,
            'TieneVehiculo' => $tieneVehiculo,
            'EstatusId' => $Personal->Status,
            'FotoURL' => $foto
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