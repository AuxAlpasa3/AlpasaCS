<div class="modal fade" id="NuevoProveedorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title">Registrar Nuevo Proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <form id="formNuevoProveedor" action="Controlador/Guardar_Proveedor.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Código:</label>
                                <input type="text" name="codigo" class="form-control" required 
                                       placeholder="PROV-001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Empresa:</label>
                                <input type="text" name="empresa" class="form-control" required 
                                       placeholder="Nombre de la empresa">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre del Contacto:</label>
                                <input type="text" name="nombre" class="form-control" required 
                                       placeholder="Nombre completo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono:</label>
                                <input type="tel" name="telefono" class="form-control" required 
                                       placeholder="5551234567">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="contacto@empresa.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RFC:</label>
                                <input type="text" name="rfc" class="form-control" 
                                       placeholder="RFC de la empresa">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dirección:</label>
                                <textarea name="direccion" class="form-control" rows="2" 
                                          placeholder="Dirección completa"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" 
                            style="background-color: #d94f00; border-color: #d94f00;">
                        <i class="fas fa-save"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formNuevoProveedor').submit(function(e) {
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
                    showNotification('Proveedor registrado correctamente', 'success');
                    $('#NuevoProveedorModal').modal('hide');
                    cargarDatosSelects();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error al guardar el proveedor', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>