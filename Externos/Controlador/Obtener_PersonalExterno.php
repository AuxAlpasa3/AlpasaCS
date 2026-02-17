<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$draw = $_POST['draw'] ?? 1;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$searchValue = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDirection = $_POST['order'][0]['dir'] ?? 'asc';

$numeroIdentificacion = $_POST['filtro_numeroIdentificacion'] ?? '';
$nombre = $_POST['filtro_nombre'] ?? '';
$cargo = $_POST['filtro_cargo'] ?? '';
$areaVisita = $_POST['filtro_areaVisita'] ?? '';
$personalResponsable = $_POST['filtro_personalResponsable'] ?? '';
$estatus = $_POST['filtro_estatus'] ?? '';

$imagenPorDefecto = 'https://intranet.alpasamx.com/regentsalper/imagenes/empleados/Default.jpg';

$columns = [
    0 => 't1.NumeroIdentificacion',
    1 => null,
    2 => 't1.Nombre',
    3 => 't1.ApPaterno',
    4 => 't1.ApMaterno',
    5 => 't1.EmpresaProcedencia',
    6 => 't3.NomCargo',
    7 => 't4.NomLargo',
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
    $queryBase = "SELECT 
                    t1.IdPersonalExterno,
                    t1.NumeroIdentificacion,
                    t1.RutaFoto,
                    t1.Nombre,
                    t1.ApPaterno,
                    t1.ApMaterno,
                    t1.EmpresaProcedencia,
                    t3.NomCargo AS Cargo,
                    CASE 
                        WHEN t1.AreaVisita = 0 OR t1.AreaVisita IS NULL THEN 'TODOS'
                        ELSE t4.NomLargo 
                    END as AreaVisita,
                    t1.IdPersonalResponsable,
                    t1.Email,
                    t1.Telefono,
                    t1.Status,
                    t1.FechaRegistro,
                    t1.VigenciaAcceso,
                    CASE 
                        WHEN t2.IdPersonal IS NOT NULL 
                        THEN CONCAT(ISNULL(t2.Nombre,''), ' ', ISNULL(t2.ApPaterno,''), ' ', ISNULL(t2.ApMaterno,''))
                        ELSE 'No asignado'
                    END as NombreResponsable,
                    t2.Email as ResEmail,
                    t2.Contacto as ResTelefono
                  FROM t_personal_externo as t1
                  LEFT JOIN t_personal as t2 ON t1.IdPersonalResponsable = t2.IdPersonal
                  LEFT JOIN t_cargoExterno as t3 on t1.Cargo = t3.IdCargo
                  LEFT JOIN t_ubicacion_interna as t4 on t1.AreaVisita = t4.IdUbicacion
                  WHERE 1=1";
    
    $countTotalQuery = "SELECT COUNT(*) as total FROM t_personal_externo";
    $stmtCountTotal = $Conexion->query($countTotalQuery);
    $totalRecords = $stmtCountTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    $conditions = array("t1.Status != '2'");
    $params = array();
    
    if (!empty($numeroIdentificacion)) {
        $conditions[] = "t1.NumeroIdentificacion LIKE :numeroIdentificacion";
        $params[':numeroIdentificacion'] = "%$numeroIdentificacion%";
    }
    
    if (!empty($nombre)) {
        $conditions[] = "(t1.Nombre LIKE :nombre OR t1.ApPaterno LIKE :nombre OR t1.ApMaterno LIKE :nombre OR CONCAT(t1.Nombre, ' ', t1.ApPaterno, ' ', t1.ApMaterno) LIKE :nombreCompleto)";
        $params[':nombre'] = "%$nombre%";
        $params[':nombreCompleto'] = "%$nombre%";
    }
    
    if (!empty($cargo)) {
        $conditions[] = "t1.Cargo = :cargo";
        $params[':cargo'] = $cargo;
    }
    
    if (!empty($areaVisita)) {
        $conditions[] = "t1.AreaVisita = :areaVisita";
        $params[':areaVisita'] = $areaVisita;
    }
    
    if (!empty($personalResponsable)) {
        $conditions[] = "t1.IdPersonalResponsable = :personalResponsable";
        $params[':personalResponsable'] = $personalResponsable;
    }
    
    if ($estatus !== '') {
        $conditions[] = "t1.Status = :estatus";
        $params[':estatus'] = $estatus;
    }
    
    if (!empty($searchValue)) {
        $conditions[] = "(t1.NumeroIdentificacion LIKE :search 
                        OR t1.Nombre LIKE :search 
                        OR t1.ApPaterno LIKE :search 
                        OR t1.ApMaterno LIKE :search 
                        OR t1.EmpresaProcedencia LIKE :search 
                        OR t3.NomCargo LIKE :search 
                        OR t4.NomLargo LIKE :search 
                        OR t1.Email LIKE :search 
                        OR t1.Telefono LIKE :search
                        OR CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) LIKE :search)";
        $params[':search'] = "%$searchValue%";
    }
    
    $whereClause = "";
    if (!empty($conditions)) {
        $whereClause = " AND " . implode(" AND ", $conditions);
    }
    
    $queryFiltered = $queryBase . $whereClause;
    $countQuery = "SELECT COUNT(*) as filtered FROM ($queryFiltered) as filtered_table";
    
    $stmtCountFiltered = $Conexion->prepare($countQuery);
    foreach ($params as $key => $value) {
        $stmtCountFiltered->bindValue($key, $value);
    }
    $stmtCountFiltered->execute();
    $filteredRecords = $stmtCountFiltered->fetch(PDO::FETCH_ASSOC)['filtered'];
    
    $queryFinal = $queryFiltered . " ORDER BY {$orderColumn} {$orderDirection}";
    
    if ($length > 0) {
        $queryFinal .= " OFFSET :start ROWS FETCH NEXT :length ROWS ONLY";
    }
    
    $stmtFinal = $Conexion->prepare($queryFinal);
    foreach ($params as $key => $value) {
        $stmtFinal->bindValue($key, $value);
    }
    
    if ($length > 0) {
        $stmtFinal->bindValue(':start', $start, PDO::PARAM_INT);
        $stmtFinal->bindValue(':length', $length, PDO::PARAM_INT);
    }
    
    $stmtFinal->execute();
    $PersonalesExternos = $stmtFinal->fetchAll(PDO::FETCH_OBJ);
    
    $data = array();
    foreach($PersonalesExternos as $Personal) {
        $badge_class = 'badge-secondary';
        $badge_text = 'Desconocido';
        
        if ($Personal->Status == '1') {
            $badge_class = 'badge-success';
            $badge_text = 'Activo';
        } elseif ($Personal->Status == '0') {
            $badge_class = 'badge-warning';
            $badge_text = 'Inactivo';
        } elseif ($Personal->Status == '2') {
            $badge_class = 'badge-danger';
            $badge_text = 'Baja';
        } elseif ($Personal->Status == '3') {
            $badge_class = 'badge-info';
            $badge_text = 'Vacaciones';
        }
        
        $estatusBtnClass = 'btn-secondary';
        $estatusBtnText = 'Cambiar';
        $estatusBtnIcon = 'fa-exchange-alt';
        
        if ($Personal->Status == '1') {
            $estatusBtnClass = 'btn-warning';
            $estatusBtnText = 'Inactivar';
        } elseif ($Personal->Status == '0') {
            $estatusBtnClass = 'btn-success';
            $estatusBtnText = 'Activar';
        } elseif ($Personal->Status == '3') {
            $estatusBtnClass = 'btn-info';
            $estatusBtnText = 'Reactivar';
        }
        
        $fotoHTML = '';
        if(!empty($Personal->RutaFoto) && filter_var($Personal->RutaFoto, FILTER_VALIDATE_URL)) {
            $fotoHTML = '
                <img src="' . htmlspecialchars($Personal->RutaFoto) . '" 
                     width="70" 
                     height="70" 
                     alt="Foto de ' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno) . '"
                     class="employee-photo thumbnail-image btn-ver-foto"
                     data-tipo="ver-foto"
                     data-full-image="' . htmlspecialchars($Personal->RutaFoto) . '"
                     data-employee-name="' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno . ' ' . $Personal->ApMaterno) . '"
                     onerror="this.onerror=null; this.src=\'' . $imagenPorDefecto . '\';">';
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
        }
        
        $responsableHTML = htmlspecialchars($Personal->NombreResponsable);
        
        $data[] = array(
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
            'Acceso' => '<a href="Controlador/Credencial.php?id=' . $Personal->IdPersonalExterno . '" 
                          class="btn btn-info btn-sm" 
                          target="_blank"
                          title="Generar Documento de Acceso">
                         <i class="fas fa-file-alt"></i>
                         </a>',
            'Acciones' => '
                <div class="btn-group" role="group">
                    <button type="button" 
                            class="btn-editar btn btn-warning btn-sm mr-1" 
                            data-tipo="editar"
                            data-id="' . $Personal->IdPersonalExterno . '"
                            title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" 
                            class="btn-cambiar-estatus btn ' . $estatusBtnClass . ' btn-sm mr-1" 
                            data-tipo="cambiar-estatus"
                            data-id="' . $Personal->IdPersonalExterno . '"
                            title="' . $estatusBtnText . '">
                        <i class="fas ' . $estatusBtnIcon . '"></i>
                    </button>
                    <button type="button" 
                            class="btn-eliminar btn btn-danger btn-sm" 
                            data-tipo="eliminar"
                            data-id="' . $Personal->IdPersonalExterno . '"
                            data-nombre="' . htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno) . '"
                            title="Eliminar">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>'
        );
    }
    
    echo json_encode(array(
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $data
    ));
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "draw" => intval($draw),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "error" => "Error en la consulta: " . $e->getMessage()
    ));
}
?>