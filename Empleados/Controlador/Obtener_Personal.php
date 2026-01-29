<?php
include '../../api/db/conexion.php';

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

$orderColumn = $columns[$orderColumnIndex] ?? 't1.IdPersonal';
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
                    t1.status as Acceso,
                    (CASE when t1.Cargo=0 then 'Sin Cargo' else t3.NomCargo END) AS NomCargo, 
                    (CASE when t1.Departamento=0 THEN 'SinDepto' else t4.NomDepto END) AS NomDepto,
                    (CASE when t1.Empresa=0 then 'SinEmpresa' else t2.NomEmpresa END) AS NomEmpresa,
                    (CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' when t1.Status=2 then 'Baja' when t1.Status=3 then 'Vacaciones' END) as StatusTexto,
                    (CASE when t1.IdUbicacion=0 then 'SinUbicacion' else t5.NomLargo end) as NomCorto 
                  FROM t_Personal as t1 
                  LEFT JOIN t_empresa as t2 on t1.Empresa=t2.IdEmpresa 
                  LEFT JOIN t_cargo as t3 on t1.Cargo=t3.IdCargo 
                  LEFT JOIN t_departamento as t4 on t4.IdDepartamento=t1.Departamento 
                  LEFT JOIN t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion
                  WHERE NoEmpleado > 0";
    
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
        } elseif ($estatus === 'Vacaciones') {
            $queryFiltered .= " AND t1.Status = 3";
        }
    }
    
    if (!empty($empresa) && $empresa !== '') {
        $paramName = ":empresa_" . $paramCount++;
        $queryFiltered .= " AND t1.Empresa = $paramName";
        $searchParams[$paramName] = $empresa;
    }
    
    if (!empty($vehiculo) && $vehiculo !== '') {
        if ($vehiculo === '1') {
            $queryFiltered .= " AND EXISTS (SELECT 1 FROM t_vehiculos v WHERE v.NoEmpleado = t1.NoEmpleado AND v.Activo = 1)";
        } elseif ($vehiculo === '0') {
            $queryFiltered .= " AND NOT EXISTS (SELECT 1 FROM t_vehiculos v WHERE v.NoEmpleado = t1.NoEmpleado AND v.Activo = 1)";
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
        $queryVehiculo = "SELECT COUNT(*) as tieneVehiculo FROM t_vehiculos WHERE NoEmpleado = :noempleado AND Activo = 1";
        $stmtVehiculo = $Conexion->prepare($queryVehiculo);
        $stmtVehiculo->bindValue(':noempleado', $Personal->NoEmpleado);
        $stmtVehiculo->execute();
        $tieneVehiculo = $stmtVehiculo->fetch(PDO::FETCH_ASSOC)['tieneVehiculo'] > 0;
        
        $badgeClass = '';
        $badgeText = $Personal->StatusTexto;
        
        switch($Personal->Status) {
            case 1: $badgeClass = 'badge-success'; break;
            case 0: $badgeClass = 'badge-warning'; break;
            case 2: $badgeClass = 'badge-danger'; break;
            case 3: $badgeClass = 'badge-info'; break;
            default: $badgeClass = 'badge-secondary';
        }
        
        $accesoActivo = ($Personal->Acceso == 1);
        
        $foto = !empty($Personal->RutaFoto) ? $Personal->RutaFoto : $imagenPorDefecto;
        $nombreCompleto = $Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno;
        $nombreCompletoHTML = htmlspecialchars($nombreCompleto);
        
        $fotoHTML = '<img src="' . htmlspecialchars($foto) . '" 
                      alt="Foto" 
                      class="thumbnail-image" 
                      style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" 
                      data-full-image="' . htmlspecialchars($foto) . '" 
                      data-employee-name="' . $nombreCompletoHTML . '"
                      onerror="this.onerror=null; this.src=\'' . $imagenPorDefecto . '\';">';
        
        $vehiculoHTML = '';
        if ($tieneVehiculo) {
            $vehiculoHTML = '<a href="#" 
                                class="btn-ver-vehiculo" 
                                data-noempleado="' . htmlspecialchars($Personal->NoEmpleado) . '" 
                                data-nombre="' . $nombreCompletoHTML . '">
                                <span class="badge badge-success">Con Vehículo</span>
                             </a>';
        } else {
            $vehiculoHTML = '<span class="badge badge-secondary">Sin Vehículo</span>';
        }
        
        $accesoHTML = '';
        if ($accesoActivo) {
            $accesoHTML = '<a href="GenerarDoc?ID=' . $Personal->IdPersonal . '" 
                              class="btn btn-primary btn-sm" 
                              target="_blank"
                              title="Descargar identificación con QR">
                              <i class="fas fa-id-card"></i> ID
                           </a>';
        } else {
            $accesoHTML = '<span class="badge badge-danger" title="Acceso inactivo">Sin Acceso</span>';
        }
        
        $rowData = [
            'NoEmpleado' => htmlspecialchars($Personal->NoEmpleado),
            'Foto' => $fotoHTML,
            'Nombre' => htmlspecialchars($Personal->Nombre),
            'ApPaterno' => htmlspecialchars($Personal->ApPaterno),
            'ApMaterno' => htmlspecialchars($Personal->ApMaterno),
            'Cargo' => htmlspecialchars($Personal->NomCargo),
            'Departamento' => htmlspecialchars($Personal->NomDepto),
            'Empresa' => htmlspecialchars($Personal->NomEmpresa),
            'Estatus' => '<span class="badge ' . $badgeClass . ' p-2">' . $badgeText . '</span>',
            'Ubicacion' => htmlspecialchars($Personal->NomCorto),
            'Vehiculo' => $vehiculoHTML,
            'Acceso' => $accesoHTML
        ];
        
        $data[] = array_merge($rowData, [
            '_id' => $Personal->IdPersonal,
            '_noempleado' => $Personal->NoEmpleado,
            '_nombre_completo' => $nombreCompleto,
            '_tiene_vehiculo' => $tieneVehiculo,
            '_estatus_id' => $Personal->Status,
            '_acceso_activo' => $accesoActivo,
            '_foto_url' => $foto
        ]);
    }
    
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $data
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en la consulta: " . $e->getMessage()
    ]);
}
?>