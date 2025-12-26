<?php
include '../../api/db/conexion.php';

$imagenPorDefecto = 'https://intranet.alpasamx.com/regentsalper/imagenes/empleados/Default.jpg';

// Parámetros recibidos de DataTables
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

// Mapeo de columnas
$columns = [
    0 => 't1.NoEmpleado',
    1 => null, // Foto
    2 => 't1.Nombre',
    3 => 't1.ApPaterno',
    4 => 't1.ApMaterno',
    5 => 't3.NomCargo',
    6 => 't4.NomDepto',
    7 => 't2.NomEmpresa',
    8 => 't1.Status',
    9 => 't5.NomLargo',
    10 => null, // Acceso
    11 => null  // Acciones
];

// Columna de ordenamiento
$orderColumn = $columns[$orderColumnIndex] ?? 't1.IdPersonal';
$orderDirection = ($orderDirection == 'desc') ? 'DESC' : 'ASC';

try {
    // Consulta base para contar total de registros
    $queryCountTotal = "SELECT COUNT(*) as total 
                       FROM t_Personal as t1 
                       WHERE NoEmpleado > 0 AND t1.tipoPersonal = 1";
    
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Consulta base
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
    
    // Aplicar filtro de búsqueda
    $queryFiltered = $queryBase;
    $searchParams = [];
    
    if (!empty($searchValue)) {
        $queryFiltered .= " AND (t1.NoEmpleado LIKE :search 
                            OR t1.Nombre LIKE :search 
                            OR t1.ApPaterno LIKE :search 
                            OR t1.ApMaterno LIKE :search 
                            OR t3.NomCargo LIKE :search 
                            OR t4.NomDepto LIKE :search 
                            OR t2.NomEmpresa LIKE :search 
                            OR t5.NomLargo LIKE :search
                            OR (CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' END) LIKE :search)";
        $searchParams[':search'] = "%{$searchValue}%";
    }
    
    // Contar registros filtrados
    $countQuery = "SELECT COUNT(*) as filtered FROM ($queryFiltered) as filtered_table";
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    
    foreach ($searchParams as $param => $value) {
        $stmtCountFiltered->bindValue($param, $value);
    }
    
    $stmtCountFiltered->execute();
    $filteredRecords = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC)['filtered'];
    
    // CONSULTA FINAL MODIFICADA PARA SQL SERVER
    // SQL Server usa OFFSET-FETCH en lugar de LIMIT
    $queryFinal = $queryFiltered . " ORDER BY {$orderColumn} {$orderDirection} 
                  OFFSET :start ROWS 
                  FETCH NEXT :length ROWS ONLY";
    
    $stmtFinal = $Conexion->prepare($queryFinal);
    
    // Bind parameters
    foreach ($searchParams as $param => $value) {
        $stmtFinal->bindValue($param, $value);
    }
    $stmtFinal->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmtFinal->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
    $stmtFinal->execute();
    $Personales = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    // Preparar datos para respuesta
    $data = [];
    foreach($Personales as $Personal) {
        $badge_class = ($Personal->Status == 1) ? 'badge-success' : 'badge-danger';
        $badge_text = ($Personal->Status == 1) ? 'Activo' : 'Inactivo';
        $estatusBtnClass = ($Personal->Status == 1) ? 'btn-secondary' : 'btn-success';
        $estatusBtnText = ($Personal->Status == 1) ? 'Dar de Baja' : 'Activar';
        
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
                    <button type="button" class="btn-eliminar btn btn-danger btn-sm" data-id="' . $Personal->IdPersonal . '" title="Eliminar">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>'
        ];
    }
    
    // Respuesta JSON
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