<?php
include '../../api/db/conexion.php';

$IdVisita = $_GET['IdVisita'] ?? '';

if (empty($IdVisita)) {
    echo '<div class="alert alert-danger">ID de visita requerido</div>';
    exit;
}

try {
    $sql = "SELECT 
                v.*,
                p.NombreProveedor,
                pp.Nombre,
                pp.ApPaterno,
                pp.ApMaterno,
                a.NombreArea
            FROM Visitas v
            JOIN t_proveedores p ON v.IdProveedor = p.IdProveedor
            JOIN t_proveedor_personal pp ON v.IdProveedorPersonal = pp.IdProveedorPersonal
            JOIN Areas a ON v.IdDepartamento = a.IdDepartamento
            WHERE v.IdVisita = ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$IdVisita]);
    $visita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$visita) {
        echo '<div class="alert alert-danger">Visita no encontrada</div>';
        exit;
    }
    
    // Obtener proveedores para el select
    $sqlProveedores = "SELECT IdProveedor, NombreProveedor FROM t_proveedores WHERE Status = 1 ORDER BY NombreProveedor";
    $stmtProveedores = $Conexion->query($sqlProveedores);
    $proveedores = $stmtProveedores->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener personal del proveedor
    $sqlPersonal = "SELECT IdProveedorPersonal, Nombre, ApPaterno, ApMaterno 
                    FROM t_proveedor_personal 
                    WHERE IdProveedor = ? AND Status = 1
                    ORDER BY Nombre";
    $stmtPersonal = $Conexion->prepare($sqlPersonal);
    $stmtPersonal->execute([$visita['IdProveedor']]);
    $personal = $stmtPersonal->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener áreas
    $sqlAreas = "SELECT IdDepartamento, NombreArea FROM Areas WHERE Estatus = 1 ORDER BY NombreArea";
    $stmtAreas = $Conexion->query($sqlAreas);
    $areas = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener vehículos
    $sqlVehiculos = "SELECT IdVehiculo, Marca, Modelo, Placas 
                     FROM t_vehiculos 
                     WHERE TipoVehiculo = 3 AND Activo = 1
                     ORDER BY Marca, Modelo";
    $stmtVehiculos = $Conexion->query($sqlVehiculos);
    $vehiculos = $stmtVehiculos->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<div class="modal fade" id="ModificarVisitaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title">Modificar Visita #<?php echo $visita['IdVisita']; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <form id="formModificarVisita" action="Controlador/Modificar_Visita.php" method="POST">
                <input type="hidden" name="IdVisita" value="<?php echo $visita['IdVisita']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Proveedor:</label>
                                <select name="IdProveedor" class="form-control select2-proveedor-mod" required>
                                    <option value="">Seleccionar proveedor...</option>
                                    <?php foreach ($proveedores as $prov): ?>
                                        <option value="<?php echo $prov['IdProveedor']; ?>"
                                            <?php echo $prov['IdProveedor'] == $visita['IdProveedor'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prov['NombreProveedor']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Personal del Proveedor:</label>
                                <select name="IdProveedorPersonal" class="form-control select2-personal-mod" required>
                                    <option value="">Seleccionar personal...</option>
                                    <?php foreach ($personal as $pers): 
                                        $nombreCompleto = $pers['Nombre'];
                                        if ($pers['ApPaterno']) $nombreCompleto .= ' ' . $pers['ApPaterno'];
                                        if ($pers['ApMaterno']) $nombreCompleto .= ' ' . $pers['ApMaterno'];
                                    ?>
                                        <option value="<?php echo $pers['IdProveedorPersonal']; ?>"
                                            <?php echo $pers['IdProveedorPersonal'] == $visita['IdProveedorPersonal'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($nombreCompleto); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Área:</label>
                                <select name="IdDepartamento" class="form-control" required>
                                    <option value="">Seleccionar área...</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?php echo $area['IdDepartamento']; ?>"
                                            <?php echo $area['IdDepartamento'] == $visita['IdDepartamento'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($area['NombreArea']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vehículo:</label>
                                <select name="IdVehiculo" class="form-control">
                                    <option value="">Sin vehículo</option>
                                    <?php foreach ($vehiculos as $veh): 
                                        $texto = $veh['Marca'] . ' ' . $veh['Modelo'];
                                        if ($veh['Placas']) $texto .= ' (' . $veh['Placas'] . ')';
                                    ?>
                                        <option value="<?php echo $veh['IdVehiculo']; ?>"
                                            <?php echo $veh['IdVehiculo'] == $visita['IdVehiculo'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($texto); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <input type="date" name="FechaVisita" class="form-control" 
                                       value="<?php echo $visita['FechaVisita']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora:</label>
                                <input type="time" name="HoraVisita" class="form-control" 
                                       value="<?php echo $visita['HoraVisita']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Motivo:</label>
                                <textarea name="Motivo" class="form-control" rows="3" required><?php echo htmlspecialchars($visita['Motivo']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estatus:</label>
                                <select name="Estatus" class="form-control" required>
                                    <option value="pendiente" <?php echo $visita['Estatus'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="activo" <?php echo $visita['Estatus'] == 'activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="completado" <?php echo $visita['Estatus'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                    <option value="cancelado" <?php echo $visita['Estatus'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>QR Code:</label>
                                <input type="text" class="form-control" value="<?php echo $visita['QrCode']; ?>" readonly>
                                <small class="text-muted">Código generado automáticamente</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" 
                            style="background-color: #d94f00; border-color: #d94f00;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2-proveedor-mod').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccionar proveedor...',
        allowClear: true,
        width: '100%'
    });
    
    $('.select2-personal-mod').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccionar personal...',
        allowClear: true,
        width: '100%'
    });
    
    // Cuando cambia el proveedor, cargar su personal
    $('select[name="IdProveedor"]').change(function() {
        var proveedorId = $(this).val();
        if (proveedorId) {
            $.ajax({
                url: 'Controlador/ajax_get_personal_proveedor.php',
                type: 'GET',
                data: { IdProveedor: proveedorId },
                dataType: 'json',
                success: function(data) {
                    var select = $('select[name="IdProveedorPersonal"]');
                    select.empty().append('<option value="">Seleccionar personal...</option>');
                    
                    if (Array.isArray(data) && data.length > 0) {
                        $.each(data, function(index, item) {
                            var nombreCompleto = item.Nombre;
                            if (item.ApPaterno) nombreCompleto += ' ' + item.ApPaterno;
                            if (item.ApMaterno) nombreCompleto += ' ' + item.ApMaterno;
                            
                            select.append('<option value="' + item.IdProveedorPersonal + '">' + nombreCompleto + '</option>');
                        });
                    }
                    select.select2();
                }
            });
        }
    });
    
    // Enviar formulario
    $('#formModificarVisita').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var btn = $(this).find('button[type="submit"]');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('Visita modificada correctamente', 'success');
                    $('#ModificarVisitaModal').modal('hide');
                    // Recargar tabla
                    cargarVisitas();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error al modificar la visita', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>