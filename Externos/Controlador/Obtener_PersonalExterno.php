<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

$numeroIdentificacion = $_POST['numeroIdentificacion'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$areaVisita = $_POST['areaVisita'] ?? '';
$personalResponsable = $_POST['personalResponsable'] ?? '';
$estatus = $_POST['estatus'] ?? '';

$imagenPorDefecto = 'https://intranet.alpasamx.com/regentsalper/imagenes/empleados/Default.jpg';

$columns = [
    0 => 't1.NumeroIdentificacion',
    1 => null, 
    2 => 't1.Nombre',
    3 => 't1.ApPaterno',
    4 => 't1.ApMaterno',
    5 => 't1.EmpresaProcedencia',
    6 => 't1.Cargo',
    7 => 't1.AreaVisita',
    8 => 't2.Nombre',
    9 => 't1.Email',
    10 => 't1.Telefono',
    11 => 't1.VigenciaAcceso',
    12 => 't1.Status',
    13 => null,
    14 => null 
];

$orderColumn = $columns[$orderColumnIndex] ?? 't1.IdPersonalExterno';
$orderDirection = ($orderDirection == 'desc') ? 'DESC' : 'ASC';

try {
    $queryCountTotal = "SELECT COUNT(*) as total 
                        FROM t_personal_externo as t1 
                        WHERE t1.Status != '2' 
                        AND (t1.VigenciaAcceso IS NULL OR t1.VigenciaAcceso <= GETDATE())"; 
    
    $stmtCountTotal = $Conexion->query($queryCountTotal);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    $queryBase = "SELECT 
                    t1.IdPersonalExterno,
                    t1.NumeroIdentificacion,
                    t1.RutaFoto,
                    t1.Nombre,
                    t1.ApPaterno,
                    t1.ApMaterno,
                    t1.EmpresaProcedencia,
                    t3.NomCargo AS Cargo,
                    (case when t1.AreaVisita=0 then 'TODOS' else  t4.NomLargo end) as AreaVisita,
                    t1.IdPersonalResponsable,
                    t1.Email,
                    t1.Telefono,
                    t1.Status,
                    t1.FechaRegistro,
                    t1.VigenciaAcceso,
                    CASE 
                        WHEN t2.IdPersonal IS NOT NULL 
                        THEN CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno)
                        ELSE 'No asignado'
                    END as NombreResponsable,
                    t2.Email as ResEmail,
                    t2.Contacto as ResTelefono
                  FROM t_personal_externo as t1
                  LEFT JOIN t_personal as t2 ON t1.IdPersonalResponsable = t2.IdPersonal
                  INNER JOIN t_cargoExterno as t3 on t1.Cargo=t3.IdCargo
                  LEFT JOIN t_ubicacion_interna as t4 on t1.AreaVisita=t4.IdUbicacion
                  WHERE t1.Status != '2' 
                    AND (t1.VigenciaAcceso IS NULL OR t1.VigenciaAcceso <= GETDATE())"; 
    
    $queryFiltered = $queryBase;
    $searchParams = [];
    $paramCount = 0;
    
    if (!empty($searchValue)) {
        $paramName = ":search_" . $paramCount++;
        $queryFiltered .= " AND (t1.NumeroIdentificacion LIKE $paramName 
                            OR t1.Nombre LIKE $paramName 
                            OR t1.ApPaterno LIKE $paramName 
                            OR t1.ApMaterno LIKE $paramName 
                            OR t1.EmpresaProcedencia LIKE $paramName 
                            OR t1.Cargo LIKE $paramName 
                            OR t1.AreaVisita LIKE $paramName 
                            OR t1.Email LIKE $paramName 
                            OR t1.Telefono LIKE $paramName
                            OR CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) LIKE $paramName)";
        $searchParams[$paramName] = "%{$searchValue}%";
    }
    
    if (!empty($numeroIdentificacion)) {
        $paramName = ":numeroIdentificacion_" . $paramCount++;
        $queryFiltered .= " AND t1.NumeroIdentificacion LIKE $paramName";
        $searchParams[$paramName] = "%{$numeroIdentificacion}%";
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
        $queryFiltered .= " AND t1.Cargo LIKE $paramName";
        $searchParams[$paramName] = "%{$cargo}%";
    }
    
    if (!empty($areaVisita) && $areaVisita !== '') {
        $paramName = ":areaVisita_" . $paramCount++;
        $queryFiltered .= " AND t1.AreaVisita LIKE $paramName";
        $searchParams[$paramName] = "%{$areaVisita}%";
    }
    
    if (!empty($personalResponsable) && $personalResponsable !== '') {
        $paramName = ":personalResponsable_" . $paramCount++;
        $queryFiltered .= " AND t1.IdPersonalResponsable = $paramName";
        $searchParams[$paramName] = $personalResponsable;
    }
    
    if (!empty($estatus) && $estatus !== '') {
        $paramName = ":estatus_" . $paramCount++;
        $queryFiltered .= " AND t1.Status = $paramName";
        $searchParams[$paramName] = $estatus;
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
    $PersonalesExternos = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = [];
    foreach($PersonalesExternos as $Personal) {
        $badge_class = 'badge-secondary'; 
        $badge_text = 'Desconocido';
        
        if ($Personal->Status == '1' || $Personal->Status == 'Activo') {
            $badge_class = 'badge-success';
            $badge_text = 'Activo';
        } elseif ($Personal->Status == '0' || $Personal->Status == 'Inactivo') {
            $badge_class = 'badge-warning';
            $badge_text = 'Inactivo';
        } elseif ($Personal->Status == '2' || $Personal->Status == 'Baja') {
            $badge_class = 'badge-danger';
            $badge_text = 'Baja';
        } elseif ($Personal->Status == '3' || $Personal->Status == 'Vacaciones') {
            $badge_class = 'badge-info';
            $badge_text = 'Vacaciones';
        }
        
        $estatusBtnClass = 'btn-secondary';
        $estatusBtnText = 'Cambiar';
        $estatusBtnIcon = 'fa-exchange-alt';
        
        if ($Personal->Status == '1' || $Personal->Status == 'Activo') {
            $estatusBtnClass = 'btn-warning';
            $estatusBtnText = 'Inactivar';
        } elseif ($Personal->Status == '0' || $Personal->Status == 'Inactivo') {
            $estatusBtnClass = 'btn-success';
            $estatusBtnText = 'Activar';
        } elseif ($Personal->Status == '3' || $Personal->Status == 'Vacaciones') {
            $estatusBtnClass = 'btn-info';
            $estatusBtnText = 'Reactivar';
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
        
      $vigenciaHTML = 'Sin Vigencia';
        if (!empty($Personal->VigenciaAcceso)) {
            $vigenciaDate = new DateTime($Personal->VigenciaAcceso);
            $vigenciaHTML = $vigenciaDate->format('d/m/Y');
            
            $hoy = new DateTime();
            if ($vigenciaDate < $hoy) {
                $vigenciaHTML .= ' <span class="badge badge-danger">Expirado</span>';
            } elseif ($vigenciaDate->diff($hoy)->days <= 7) {
                $vigenciaHTML .= ' <span class="badge badge-warning">Próximo a vencer</span>';
            }
        } else {
            $vigenciaHTML = 'Sin Vigencia';
        }
        
        $responsableHTML = htmlspecialchars($Personal->NombreResponsable);
        
        $data[] = [
            'IdPersonalExterno' => $Personal->IdPersonalExterno,
            'NumeroIdentificacion' => htmlspecialchars($Personal->NumeroIdentificacion),
            'Foto' => $fotoHTML,
            'Nombre' => htmlspecialchars($Personal->Nombre),
            'ApPaterno' => htmlspecialchars($Personal->ApPaterno),
            'ApMaterno' => htmlspecialchars($Personal->ApMaterno),
            'EmpresaProcedencia' => htmlspecialchars($Personal->EmpresaProcedencia),
            'Cargo' => htmlspecialchars($Personal->Cargo),
            'AreaVisita' => htmlspecialchars($Personal->AreaVisita),
            'PersonalResponsable' => $responsableHTML,
            'Email' => htmlspecialchars($Personal->Email),
            'Telefono' => htmlspecialchars($Personal->Telefono),
            'VigenciaAcceso' => $vigenciaHTML,
            'Status' => $Personal->Status,
            'EstatusHTML' => '<span class="badge ' . $badge_class . ' p-2">' . $badge_text . '</span>',
            'Acceso' => '<a href="Controlador/GenerarDoc.php?id=' . $Personal->IdPersonalExterno . '" 
                          class="btn btn-info btn-sm" 
                          target="_blank"
                          title="Generar Documento de Acceso">
                         <i class="fas fa-file-alt"></i>
                         </a>',
            'Acciones' => '
                <div class="btn-group" role="group">
                    <button type="button" 
                            class="btn-editar btn btn-warning btn-sm mr-1" 
                            data-id="' . $Personal->IdPersonalExterno . '"
                            title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" 
                            class="btn-cambiar-estatus btn ' . $estatusBtnClass . ' btn-sm mr-1" 
                            data-id="' . $Personal->IdPersonalExterno . '"
                            title="' . $estatusBtnText . '">
                        <i class="fas ' . $estatusBtnIcon . '"></i>
                    </button>
                    <button type="button" 
                            class="btn-eliminar btn btn-danger btn-sm" 
                            data-id="' . $Personal->IdPersonalExterno . '"
                            title="Eliminar">
                        <i class="fa fa-trash"></i>
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