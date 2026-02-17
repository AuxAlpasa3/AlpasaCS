<?php

include '../../api/db/conexion.php';

$IdProveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : '';
$IdDepartamento = isset($_GET['area']) ? $_GET['area'] : '';
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : 'hoy';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';
$IdProveedorPersonal = isset($_GET['personal']) ? $_GET['personal'] : '';
$qr_code = isset($_GET['qr_code']) ? $_GET['qr_code'] : '';

try {
    // Construir consulta base
    $sql = "SELECT 
                t1.IdVisita,
                t1.QrCode,
                t1.FechaVisita,
                t1.HoraVisita,
                t1.Motivo,
                t1.Vehiculo,
                t1.Placas,
                t1.Estatus,
                t1.FechaExpiracion,
                t1.FechaIngreso,
                t1.FechaSalida,
                t2.NombreProveedor,
                t2.Email,
                t2.Telefono,
                t3.Nombre,
                t3.ApPaterno,
                t3.ApMaterno,
                t3.RutaFoto as FotoPersonal,
                t4.NomDepto,
                t5.Marca,
                t5.Modelo,
                t5.Placas as PlacasVehiculo
            FROM t_visitas_proveedores t1
            LEFT JOIN t_proveedor t2 ON t1.IdProveedor = t2.IdProveedor
            LEFT JOIN t_proveedor_personal t3 ON t1.IdProveedor = t3.IdProveedorPersonal
            LEFT JOIN t_departamento t4 ON t1.IdDepartamento = t4.IdDepartamento
            LEFT JOIN t_vehiculos t5 ON t1.Vehiculo = t5.IdVehiculo
            WHERE 1=1";
    
    $params = array();
    
    // Aplicar filtros
    if (!empty($IdProveedor)) {
        $sql .= " AND t1.IdProveedor = ?";
        $params[] = $IdProveedor;
    }
    
    if (!empty($IdDepartamento)) {
        $sql .= " AND t1.IdDepartamento = ?";
        $params[] = $IdDepartamento;
    }
    
    if (!empty($estatus)) {
        $sql .= " AND t1.Estatus = ?";
        $params[] = $estatus;
    }
    
    if (!empty($IdProveedorPersonal)) {
        $sql .= " AND t1.IdProveedorPersonal = ?";
        $params[] = $IdProveedorPersonal;
    }
    
    if (!empty($qr_code)) {
        $sql .= " AND t1.QrCode LIKE ?";
        $params[] = '%' . $qr_code . '%';
    }
    
    // Filtrar por fecha
    switch($filtro_fecha) {
        case 'hoy':
            $sql .= " AND CONVERT(DATE, t1.FechaVisita) = CONVERT(DATE, GETDATE())";
            break;
        case 'ayer':
            $sql .= " AND CONVERT(DATE, t1.FechaVisita) = CONVERT(DATE, DATEADD(DAY, -1, GETDATE()))";
            break;
        case 'semana':
            $sql .= " AND DATEPART(WEEK, t1.FechaVisita) = DATEPART(WEEK, GETDATE()) 
                     AND DATEPART(YEAR, t1.FechaVisita) = DATEPART(YEAR, GETDATE())";
            break;
        case 'mes':
            $sql .= " AND MONTH(t1.FechaVisita) = MONTH(GETDATE()) 
                     AND YEAR(t1.FechaVisita) = YEAR(GETDATE())";
            break;
        case 'personalizado':
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $sql .= " AND t1.FechaVisita BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
            }
            break;
    }
    
    $sql .= " ORDER BY t1.FechaVisita DESC, t1.HoraVisita DESC";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute($params);
    $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    
    if (count($visitas) > 0) {
        foreach($visitas as $row) {
            $badge_class = '';
            switch($row['Estatus']) {
                case 'pendiente':
                    $badge_class = 'badge-warning';
                    break;
                case 'activo':
                    $badge_class = 'badge-success';
                    break;
                case 'completado':
                    $badge_class = 'badge-info';
                    break;
                case 'cancelado':
                    $badge_class = 'badge-danger';
                    break;
                default:
                    $badge_class = 'badge-secondary';
            }
            
            $nombrePersonal = $row['Nombre'];
            if ($row['ApPaterno']) $nombrePersonal .= ' ' . $row['ApPaterno'];
            if ($row['ApMaterno']) $nombrePersonal .= ' ' . $row['ApMaterno'];
            
            $vehiculo_html = '<span class="badge badge-secondary">No</span>';
            if ($row['Vehiculo'] == 1 && $row['Marca']) {
                $placas = $row['Placas'] ?: $row['PlacasVehiculo'];
                $vehiculo_html = '<span class="badge badge-info">Sí</span><br><small>' . 
                               $row['Marca'] . ' ' . $row['Modelo'] . 
                               ($placas ? ' (' . $placas . ')' : '') . '</small>';
            }
            
            $qr_mini = '<div class="qr-mini" title="Ver QR completo" data-id="' . $row['IdVisita'] . '">
                        <svg width="40" height="40" viewBox="0 0 40 40">
                            <rect width="40" height="40" fill="#f8f9fa"/>
                            <rect x="8" y="8" width="4" height="4" fill="#000"/>
                            <rect x="8" y="16" width="4" height="4" fill="#000"/>
                            <rect x="16" y="8" width="4" height="4" fill="#000"/>
                            <rect x="24" y="16" width="4" height="4" fill="#000"/>
                            <rect x="28" y="24" width="4" height="4" fill="#000"/>
                        </svg>
                    </div>';
            
            $acciones = '<div class="btn-group" role="group">';
            $acciones .= '<button class="btn btn-sm btn-info btn-ver-detalles" data-id="' . $row['IdVisita'] . '" title="Ver detalles">
                             <i class="fas fa-eye"></i>
                          </button>';
            
            $acciones .= '<button class="btn btn-sm btn-primary btn-ver-qr" data-id="' . $row['IdVisita'] . '" title="Ver QR">
                             <i class="fas fa-qrcode"></i>
                          </button>';
            
            $acciones .= '<button class="btn btn-sm btn-warning btn-modificar" data-id="' . $row['IdVisita'] . '" title="Modificar">
                             <i class="fas fa-edit"></i>
                          </button>';
            
            if ($row['Estatus'] === 'pendiente' || $row['Estatus'] === 'activo') {
                $acciones .= '<button class="btn btn-sm btn-success btn-reenviar-qr" data-id="' . $row['IdVisita'] . '" title="Reenviar QR">
                                 <i class="fas fa-paper-plane"></i>
                              </button>';
            }
            
            if ($row['Estatus'] === 'pendiente') {
                $acciones .= '<button class="btn btn-sm btn-danger btn-cancelar" data-id="' . $row['IdVisita'] . '" title="Cancelar">
                                 <i class="fas fa-times"></i>
                              </button>';
            }
            
            if ($row['Estatus'] === 'activo') {
                $acciones .= '<button class="btn btn-sm btn-info btn-completar" data-id="' . $row['IdVisita'] . '" title="Completar">
                                 <i class="fas fa-check"></i>
                              </button>';
            }
            
            $acciones .= '<button class="btn btn-sm btn-danger btn-eliminar" data-id="' . $row['IdVisita'] . '" title="Eliminar">
                             <i class="fas fa-trash"></i>
                          </button>';
            
            $acciones .= '</div>';
            
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $row['IdVisita'] . '</td>';
            $html .= '<td class="text-center">' . $qr_mini . '</td>';
            $html .= '<td>' . $row['NombreProveedor'] . '<br><small class="text-muted">' . 
                    ($row['Email'] ?: 'Sin email') . '</small></td>';
            $html .= '<td>' . $nombrePersonal . '</td>';
            $html .= '<td>' . $row['NombreArea'] . '</td>';
            $html .= '<td>' . date('d/m/Y', strtotime($row['FechaVisita'])) . '<br><small>' . 
                    $row['HoraVisita'] . '</small></td>';
            $html .= '<td class="small">' . ($row['Motivo'] ?: 'Sin motivo') . '</td>';
            $html .= '<td class="text-center">' . $vehiculo_html . '</td>';
            $html .= '<td class="text-center"><span class="badge ' . $badge_class . '">' . 
                    $row['Estatus'] . '</span></td>';
            $html .= '<td class="text-center">' . $acciones . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr class="no-data"><td colspan="10" class="text-center text-muted py-4">
                    <i class="fas fa-search fa-2x mb-2"></i><br>
                    No se encontraron visitas con los filtros aplicados
                </td></tr>';
    }
    
    echo $html;
    
} catch (PDOException $e) {
    echo '<tr><td colspan="10" class="text-center text-danger">Error: ' . $e->getMessage() . '</td></tr>';
}
?>