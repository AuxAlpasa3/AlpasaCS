<?php
include '../../api/db/conexion.php';

$NoEmpleado = $_GET['NoEmpleado'] ?? '';

if (empty($NoEmpleado)) {
    echo '<div class="alert alert-warning text-center">';
    echo '<i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>';
    echo 'No se especificó el número de empleado';
    echo '</div>';
    exit;
}

try {
    $query = "SELECT t1.*, 
                (CASE WHEN t1.Activo = 1 THEN 'Activo' ELSE 'Inactivo' END) as EstadoTexto
            FROM t_vehiculos t1 
            WHERE t1.NoEmpleado = :noempleado 
            ORDER BY t1.Activo DESC, t1.Marca, t1.Modelo";
                
    $stmt = $Conexion->prepare($query);
    $stmt->bindValue(':noempleado', $NoEmpleado, PDO::PARAM_STR);
    $stmt->execute();
    
    $vehiculos = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    if (count($vehiculos) > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-hover table-sm">';
        echo '<thead class="thead-light">';
        echo '<tr>';
        echo '<th>Marca</th>';
        echo '<th>Modelo</th>';
        echo '<th>Placas</th>';
        echo '<th>Color</th>';
        echo '<th>Año</th>';
        echo '<th>Núm. Serie</th>';
        echo '<th>Estado</th>';
        echo '<th>Foto</th>';
        echo '<th>Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($vehiculos as $vehiculo) {
            $badge_class = ($vehiculo->Activo == 1) ? 'badge-success' : 'badge-danger';
            $badge_text = ($vehiculo->Activo == 1) ? 'Activo' : 'Inactivo';
            
            $fotoHTML = '';
            if (!empty($vehiculo->RutaFoto) && filter_var($vehiculo->RutaFoto, FILTER_VALIDATE_URL)) {
                $fotoHTML = '<button class="btn btn-sm btn-info btn-ver-foto-vehiculo" 
                                    data-image="' . htmlspecialchars($vehiculo->RutaFoto) . '" 
                                    data-info="' . htmlspecialchars($vehiculo->Marca . ' ' . $vehiculo->Modelo . ' - ' . $vehiculo->Placas) . '"
                                    title="Ver foto">
                                <i class="fas fa-eye"></i>
                             </button>';
            } else {
                $fotoHTML = '<span class="text-muted small"><i class="fas fa-ban"></i> Sin foto</span>';
            }
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($vehiculo->Marca) . '</td>';
            echo '<td>' . htmlspecialchars($vehiculo->Modelo) . '</td>';
            echo '<td><strong>' . htmlspecialchars($vehiculo->Placas) . '</strong></td>';
            echo '<td>' . htmlspecialchars($vehiculo->Color) . '</td>';
            echo '<td>' . htmlspecialchars($vehiculo->Anio) . '</td>';
            echo '<td><small class="text-muted">' . htmlspecialchars($vehiculo->Num_Serie) . '</small></td>';
            echo '<td class="text-center"><span class="badge ' . $badge_class . ' p-1">' . $badge_text . '</span></td>';
            echo '<td class="text-center">' . $fotoHTML . '</td>';
            echo '<td class="text-center">';
            echo '<button class="btn btn-sm btn-danger btn-eliminar-vehiculo" 
                          data-id="' . $vehiculo->IdVehiculo . '" 
                          data-noempleado="' . $vehiculo->NoEmpleado . '"
                          data-placas="' . htmlspecialchars($vehiculo->Placas) . '"
                          data-modelo="' . htmlspecialchars($vehiculo->Marca . ' ' . $vehiculo->Modelo) . '"
                          title="Eliminar vehículo">
                    <i class="fas fa-trash"></i>
                  </button>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        
        // Mostrar contador
        echo '<div class="mt-3 text-right">';
        echo '<small class="text-muted">Total: <strong>' . count($vehiculos) . '</strong> vehículo(s) asignado(s)</small>';
        echo '</div>';
        
    } else {
        echo '<div class="alert alert-info text-center py-4">';
        echo '<i class="fas fa-car fa-3x mb-3 text-muted"></i>';
        echo '<h5 class="mb-2">No hay vehículos asignados</h5>';
        echo '<p class="mb-0 text-muted">Este empleado no tiene vehículos asignados.</p>';
        echo '<p class="small text-muted mt-2">Use el formulario superior para agregar un vehículo.</p>';
        echo '</div>';
    }
    
} catch(PDOException $e) {
    // Manejo de errores similar al Obtener_Personal.php
    echo '<div class="alert alert-danger text-center py-4">';
    echo '<i class="fas fa-exclamation-triangle fa-2x mb-2"></i>';
    echo '<h5 class="mb-2">Error al cargar los vehículos</h5>';
    echo '<p class="mb-0 text-muted">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    
    // También puedes loguear el error para debugging
    error_log('Error en Obtener_Vehiculos.php: ' . $e->getMessage());
}
?>