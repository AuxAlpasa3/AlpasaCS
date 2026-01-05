<?php
include '../../api/db/conexion.php';

// Obtener parámetros de DataTables
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

// Obtener filtros personalizados desde Catalogos.php
$noempleado = $_POST['noempleado'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$departamento = $_POST['departamento'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$estatus = $_POST['estatus'] ?? '';
$empresa = $_POST['empresa'] ?? '';

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
    // Consulta base sin filtros WHERE para contar total
    $queryCountTotal = "SELECT COUNT(*) as total 
                       FROM t_Personal as t1 
                       WHERE NoEmpleado > 0 AND t1.tipoPersonal = 1";
    
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Consulta base para datos
    $queryBase = "SELECT t1.IdPersonal,t1.NoEmpleado,t1.RutaFoto,t1.Nombre,t1.ApPaterno,t1.ApMaterno,
                         t1.Cargo,t1.Departamento,t1.Empresa,t1.Status,t1.IdUbicacion,
                         (CASE when t1.Cargo=0 then 'Sin Cargo' else t3.NomCargo END) AS NomCargo, 
                         (CASE when t1.Departamento=0 THEN 'SinDepto' else t4.NomDepto END) AS NomDepto,
                         (CASE when t1.Empresa=0 then 'SinEmpresa' else t2.NomEmpresa END) AS NomEmpresa,
                         (CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' END) as StatusTexto,
                         (CASE when t1.IdUbicacion=0 then 'SinUbicacion' else t5.NomLargo end) as NomCorto 
                  FROM t_Personal as t1 
                  LEFT JOIN t_empresa as t2 on t1.Empresa=t2.IdEmpresa 
                  LEFT JOIN t_cargo as t3 on t1.Cargo=t3.IdCargo 
                  LEFT JOIN t_departamento as t4 on t4.IdDepartamento=t1.Departamento 
                  LEFT JOIN t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion
                  WHERE NoEmpleado > 0 AND t1.tipoPersonal = 1";
    
    $queryFiltered = $queryBase;
    $searchParams = [];
    $paramCount = 0;
    
    // Aplicar filtro de búsqueda general de DataTables
    if (!empty($searchValue)) {
        $paramName = ":search_" . $paramCount++;
        $queryFiltered .= " AND (t1.NoEmpleado LIKE $paramName 
                            OR t1.Nombre LIKE $paramName 
                            OR t1.ApPaterno LIKE $paramName 
                            OR t1.ApMaterno LIKE $paramName 
                            OR t3.NomCargo LIKE $paramName 
                            OR t4.NomDepto LIKE $paramName 
                            OR t2.NomEmpresa LIKE $paramName 
                            OR t5.NomLargo LIKE $paramName
                            OR (CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' END) LIKE $paramName)";
        $searchParams[$paramName] = "%{$searchValue}%";
    }
    
    // Aplicar filtros personalizados
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
            // Si tienes un campo para baja, ajusta según tu estructura
            $queryFiltered .= " AND t1.Status = 2"; // Asumiendo que 2 es baja
        } elseif ($estatus === 'Vacaciones') {
            // Si tienes un campo para vacaciones, ajusta según tu estructura
            $queryFiltered .= " AND t1.Status = 3"; // Asumiendo que 3 es vacaciones
        }
    }
    
    if (!empty($empresa) && $empresa !== '') {
        $paramName = ":empresa_" . $paramCount++;
        $queryFiltered .= " AND t1.Empresa = $paramName";
        $searchParams[$paramName] = $empresa;
    }
    
    // Contar registros filtrados
    $countQuery = "SELECT COUNT(*) as filtered FROM ($queryFiltered) as filtered_table";
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    
    foreach ($searchParams as $param => $value) {
        $stmtCountFiltered->bindValue($param, $value);
    }
    
    $stmtCountFiltered->execute();
    $filteredRecords = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC)['filtered'];
    
    // Agregar ordenamiento y paginación
    $queryFinal = $queryFiltered . " ORDER BY {$orderColumn} {$orderDirection} 
                  OFFSET :start ROWS 
                  FETCH NEXT :length ROWS ONLY";
    
    $stmtFinal = $Conexion->prepare($queryFinal);
    
    // Bind de parámetros de búsqueda
    foreach ($searchParams as $param => $value) {
        $stmtFinal->bindValue($param, $value);
    }
    
    // Bind de parámetros de paginación
    $stmtFinal->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmtFinal->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
    $stmtFinal->execute();
    $Personales = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = [];
    foreach($Personales as $Personal) {
        // Determinar badge según Status
        $badge_class = 'badge-secondary'; // Por defecto
        $badge_text = 'Desconocido';
        
        if ($Personal->Status == 1) {
            $badge_class = 'badge-success';
            $badge_text = 'Activo';
        } elseif ($Personal->Status == 0) {
            $badge_class = 'badge-warning';
            $badge_text = 'Inactivo';
        } elseif ($Personal->Status == 2) {
            $badge_class = 'badge-danger';
            $badge_text = 'Baja';
        } elseif ($Personal->Status == 3) {
            $badge_class = 'badge-info';
            $badge_text = 'Vacaciones';
        }
        
        // Botón de cambiar estatus
        $estatusBtnClass = 'btn-secondary';
        $estatusBtnText = 'Cambiar';
        
        if ($Personal->Status == 1) {
            $estatusBtnClass = 'btn-warning';
            $estatusBtnText = 'Dar de Baja';
        } elseif ($Personal->Status == 0) {
            $estatusBtnClass = 'btn-success';
            $estatusBtnText = 'Activar';
        }
        
        // Foto
        $fotoHTML = '';
        if(!empty($Personal->RutaFoto) && filter_var($Personal->RutaFoto, FILTER_VALIDATE_URL)) {
            $fotoHTML = '
                <img src="' . htmlspecialchars($Personal->RutaFoto) . '" 
                     width="70" 
                     height="70" 
                     alt="Foto de ' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno) . '"
                     class="employee-photo thumbnail-image"
                     data-full-image="' . htmlspecialchars($Personal->RutaFoto) . '"
                     data-employee-name="' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno) . '"
                     onerror="this.onerror=null; this.src=\'' . $imagenPorDefecto . '\';">
                <br>
                <small>
                    <a href="#" 
                       class="view-photo-link" 
                       data-full-image="' . htmlspecialchars($Personal->RutaFoto) . '"
                       data-employee-name="' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno) . '">
                    </a>
                </small>';
        } else {
            $fotoHTML = '<img src="' . $imagenPorDefecto . '" 
                             width="70" 
                             height="70" 
                             alt="Sin foto"
                             class="employee-photo">';
        }
        
        $data[] = [
            'NoEmpleado' => htmlspecialchars($Personal->NoEmpleado),
            'Foto' => $fotoHTML,
            'Nombre' => htmlspecialchars($Personal->Nombre),
            'ApPaterno' => htmlspecialchars($Personal->ApPaterno),
            'ApMaterno' => htmlspecialchars($Personal->ApMaterno),
            'Cargo' => htmlspecialchars($Personal->NomCargo),
            'Departamento' => htmlspecialchars($Personal->NomDepto),
            'Empresa' => htmlspecialchars($Personal->NomEmpresa),
            'Estatus' => '<span class="badge ' . $badge_class . ' p-2">' . $badge_text . '</span>',
            'Ubicacion' => htmlspecialchars($Personal->NomCorto),
            // MANTENIENDO EL BOTÓN DE DESCARGA ORIGINAL
            'Acceso' => '<a href="GenerarDoc?ID=' . $Personal->IdPersonal . '" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-download"></i> Descargar</a>',
            'Acciones' => '
                <div class="btn-group" role="group">
                    <button type="button" class="btn-editar btn btn-warning btn-sm" data-id="' . $Personal->IdPersonal . '">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" 
                            class="btn-cambiar-estatus btn ' . $estatusBtnClass . ' btn-sm" 
                            data-id="' . $Personal->IdPersonal . '"
                            title="' . $estatusBtnText . '">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>'
        ];
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