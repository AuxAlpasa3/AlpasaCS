<?php
include_once "../../templates/Sesion.php";

$almacenes = $Conexion->query("SELECT t1.IdAlmacen, concat(t1.IdAlmacen,'-' ,t1.Almacen) as Almacen, t1.NumRecinto 
                               FROM t_almacen as t1 
                               INNER JOIN t_usuario_almacen as t2 on t1.IdAlmacen=t2.IdAlmacen 
                               WHERE t2.IdUsuario=$IdUsuario 
                               ORDER BY t1.Almacen")->fetchAll(PDO::FETCH_OBJ);

$clientes = [];
$tiposRemision = [];
$articulos = [];

if (count($almacenes) > 0) {
    $primerAlmacen = $almacenes[0]->IdAlmacen;
    $primerRecinto = $almacenes[0]->NumRecinto;
    
    $consecutivoQuery = $Conexion->query("SELECT COALESCE(MAX(IdRemisionEncabezado), 0) + 1 AS siguiente FROM t_remision_encabezado WHERE Almacen = $primerAlmacen");
    $consecutivo = $consecutivoQuery->fetch(PDO::FETCH_OBJ);
    $siguienteId = $consecutivo->siguiente;
    
    $idRemisionFormateado = "REM-" . $primerRecinto . "-" . str_pad($siguienteId, 2, '0', STR_PAD_LEFT);
    
    $clientes = $Conexion->query("SELECT t1.IdCliente, t1.NombreCliente,t1.RFC,t1.direccion FROM t_cliente as t1
                                INNER JOIN t_cliente_almacen as t2 on t1.IdCliente=t2.Idcliente
                                INNER JOIN t_usuario_almacen as t3 on t2.IdAlmacen= t3.IdAlmacen
                                Where t3.IdUsuario=$IdUsuario ORDER BY NombreCliente")->fetchAll(PDO::FETCH_OBJ);

    $tiposRemision = $Conexion->query("SELECT IdTipoRemision, TipoRemision FROM t_tipoRemision ORDER BY TipoRemision")->fetchAll(PDO::FETCH_OBJ);

    $articulos = $Conexion->query("SELECT DISTINCT(t1.IdArticulo), t1.MaterialNo, CONCAT(t1.Material,' ',t1.Shape) AS MaterialShape FROM t_articulo AS t1 
                            INNER JOIN t_articulo_almacen as t2 on t1.IdArticulo=T2.IdArticulo
                            INNER JOIN t_usuario_almacen as t3 on t2.IdAlmacen=T3.IdAlmacen
                            where t3.IdUsuario=$IdUsuario")->fetchAll(PDO::FETCH_OBJ);
}
?>

<div class="modal fade" id="nuevaRemision" tabindex="-1" role="dialog" aria-labelledby="nuevaRemisionTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="nuevaRemisionTitle">Nueva Remisión</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="AgregarRemision" name="AgregarRemision" method="POST" action="ProcesoRemision/GuardarRemision.php" enctype="multipart/form-data">
                    <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?= htmlspecialchars($IdUsuario) ?>">
                    <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado" value="<?= htmlspecialchars($siguienteId) ?>">
                    
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="IdRemision" class="small font-weight-bold">ID Remisión</label>
                            <input type="text" class="form-control form-control-sm" name="IdRemision" id="IdRemision" 
                                   value="<?= htmlspecialchars($idRemisionFormateado) ?>" readonly>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="Almacen" class="small font-weight-bold">Almacen <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm select2-almacen" name="Almacen" id="Almacen" required 
                                    data-recinto="<?= htmlspecialchars($primerRecinto) ?>">
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach ($almacenes as $almacen): ?>
                                    <option value="<?= htmlspecialchars($almacen->IdAlmacen) ?>" 
                                            data-recinto="<?= htmlspecialchars($almacen->NumRecinto) ?>">
                                        <?= htmlspecialchars($almacen->Almacen) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="TipoRemision" class="small font-weight-bold">Tipo <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="TipoRemision" id="TipoRemision" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach ($tiposRemision as $tipo): ?>
                                    <option value="<?= htmlspecialchars($tipo->IdTipoRemision) ?>">
                                        <?= htmlspecialchars($tipo->TipoRemision) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="Fecha" class="small font-weight-bold">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="Fecha" name="Fecha" required>
                        </div>
                    </div>

                    <!-- Segunda Fila: Solo Cliente -->
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="Cliente" class="small font-weight-bold">Cliente <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm select2-cliente" name="Cliente" id="Cliente" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= htmlspecialchars($cliente->IdCliente) ?>"
                                        data-rfc="<?= htmlspecialchars($cliente->RFC) ?>"
                                        data-direccion="<?= htmlspecialchars($cliente->direccion) ?>">
                                        <?= htmlspecialchars($cliente->NombreCliente) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Información del Cliente (Oculta por defecto) -->
                    <div class="row" id="cliente-info" style="display: none;">
                        <div class="col-md-6 form-group">
                            <label for="RFC" class="small font-weight-bold">RFC</label>
                            <input type="text" class="form-control form-control-sm" id="RFC" name="RFC" readonly>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Direccion" class="small font-weight-bold">Dirección</label>
                            <input type="text" class="form-control form-control-sm" id="Direccion" name="Direccion" readonly>
                        </div>
                    </div>

                    <!-- Tercera Fila: Transportista, Placas, Chofer -->
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="Transportista" class="small font-weight-bold">Transportista</label>
                            <input type="text" class="form-control form-control-sm" id="Transportista" name="Transportista"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Transportista">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="Placas" class="small font-weight-bold">Placas</label>
                            <input type="text" class="form-control form-control-sm" id="Placas" name="Placas"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Placas">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="Chofer" class="small font-weight-bold">Chofer</label>
                            <input type="text" class="form-control form-control-sm" id="Chofer" name="Chofer"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Chofer">
                        </div>
                    </div>

                    <!-- Cuarta Fila: Contenedor, Caja, Tracto, Sellos -->
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="Contenedor" class="small font-weight-bold">Contenedor</label>
                            <input type="text" class="form-control form-control-sm" id="Contenedor" name="Contenedor"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Contenedor">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="Caja" class="small font-weight-bold">Caja</label>
                            <input type="text" class="form-control form-control-sm" id="Caja" name="Caja"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Caja">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="Tracto" class="small font-weight-bold">Tracto</label>
                            <input type="text" class="form-control form-control-sm" id="Tracto" name="Tracto"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Tracto">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="Sellos" class="small font-weight-bold">Sellos</label>
                            <input type="text" class="form-control form-control-sm" id="Sellos" name="Sellos"
                                onkeyup="this.value = this.value.toUpperCase()" placeholder="Sellos">
                        </div>
                    </div>

                    <!-- Sección de Materiales -->
                    <div class="mt-3" id="seccion-materiales">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 font-weight-bold">Materiales</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" onclick="agregarFila()">
                                    <i class="fas fa-plus"></i> Añadir
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="quitarUltimaFila()">
                                    <i class="fas fa-minus"></i> Quitar
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 200px;">
                            <table id="TablaRem" class="table table-sm table-bordered table-hover mb-2">
                                <thead>
                                    <tr>
                                        <th width="8%" class="text-center small">#</th>
                                        <th width="22%" class="text-center small">Material No.</th>
                                        <th width="30%" class="text-center small">Material/Shape</th>
                                        <th width="15%" class="text-center small">Booking</th>
                                        <th width="10%" class="text-center small">Piezas</th>
                                        <th width="10%" class="text-center small">Cantidad</th>
                                        <th width="5%" class="text-center small"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            <input type="text" class="form-control form-control-sm text-center" name="IdLinea[]" value="1" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm material-no" name="MaterialNo[]" required>
                                                <option value="" selected>Seleccione...</option>
                                                <?php foreach ($articulos as $articulo): ?>
                                                    <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                                                        <?= htmlspecialchars($articulo->MaterialNo) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm material-shape" name="Articulo[]" required>
                                                <option value="" selected>Seleccione...</option>
                                                <?php foreach ($articulos as $articulo): ?>
                                                    <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                                                        <?= htmlspecialchars($articulo->MaterialShape) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="Booking[]" 
                                                   onkeyup="this.value = this.value.toUpperCase()">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center" name="Piezas[]" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center" name="Cantidad[]" min="1" step="1" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="eliminarFila(this)" title="Eliminar">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary" name="Mov" value="AgregarRemision" id="btnGuardar">
                            <i class="fas fa-save mr-1"></i> Guardar Remisión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container {
        z-index: 9999 !important;
    }

    .select2-dropdown {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
    }

    .modal-lg {
        max-width: 900px !important;
    }

    .modal-body {
        padding: 15px;
        font-size: 0.9rem;
    }

    .select2-container .select2-selection--single {
        height: 32px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 0.875rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
    }

    .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }

    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .thead-dark th {
        background-color: #495057;
        color: white;
        font-weight: 500;
        vertical-align: middle;
        font-size: 0.8rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }

    .small {
        font-size: 0.8rem;
    }

    .form-group {
        margin-bottom: 0.7rem;
    }

    /* Estilos para campos requeridos */
    .is-invalid {
        border-color: #dc3545 !important;
    }

    /* Estilo para campos de solo lectura */
    .form-control[readonly] {
        background-color: #f8f9fa;
        opacity: 1;
    }

    /* Ajustes para Select2 en modales */
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
    }

    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95% !important;
            margin: 10px;
        }

        .modal-body {
            padding: 10px;
        }

        .table-responsive {
            max-height: 150px;
        }

        .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }
</style>

<script>
    $(document).ready(function () {
        var select2Options = {
            dropdownParent: $('#nuevaRemision'),
            width: '100%',
            dropdownAutoWidth: true,
            placeholder: "Seleccione...",
            allowClear: false
        };

        // Inicializar Select2
        $('.select2-almacen, .select2-cliente').select2(select2Options);
        $('#TablaRem tbody tr:first .material-no, #TablaRem tbody tr:first .material-shape').select2(select2Options);

        // Establecer fecha actual por defecto
        $('#Fecha').val(new Date().toISOString().substr(0, 10));

        // Función para verificar y mostrar/ocultar materiales
        function verificarMateriales() {
            var tipoRemisionTexto = $('#TipoRemision option:selected').text().toLowerCase();
            
            // Verificar si el tipo de remisión es "Salida"
            if (tipoRemisionTexto.includes('salida')) {
                $('#seccion-materiales').hide();
                // Quitar atributo required de los campos de materiales cuando están ocultos
                $('#TablaRem [name="MaterialNo[]"], #TablaRem [name="Articulo[]"], #TablaRem [name="Piezas[]"], #TablaRem [name="Cantidad[]"]').removeAttr('required');
            } else {
                $('#seccion-materiales').show();
                // Agregar atributo required a los campos de materiales cuando están visibles
                $('#TablaRem [name="MaterialNo[]"], #TablaRem [name="Articulo[]"], #TablaRem [name="Piezas[]"], #TablaRem [name="Cantidad[]"]').attr('required', 'required');
            }
        }

        // Ejecutar al cargar para verificar el estado inicial
        verificarMateriales();

        // Manejar cambio de almacén para actualizar el ID de remisión
        $('#Almacen').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var idAlmacen = $(this).val();
            var recinto = selectedOption.data('recinto');
            
            if (idAlmacen && recinto) {
                actualizarIdRemision(idAlmacen, recinto);
            }
        });

        // Manejar cambio de tipo de remisión para verificar materiales
        $('#TipoRemision').on('change', function() {
            verificarMateriales();
        });

        // Manejar cambio de cliente
        $('#Cliente').on('change', function () {
            var selectedOption = $(this).find('option:selected');
            var rfc = selectedOption.data('rfc');
            var direccion = selectedOption.data('direccion');

            if (rfc && direccion) {
                $('#RFC').val(rfc);
                $('#Direccion').val(direccion);
                $('#cliente-info').show();
            } else {
                $('#RFC').val('');
                $('#Direccion').val('');
                $('#cliente-info').hide();
            }
        });

        // Limpiar el modal cuando se cierra
        $('#nuevaRemision').on('hidden.bs.modal', function () {
            $('#AgregarRemision')[0].reset();
            $('.select2-almacen, .select2-cliente').val('').trigger('change');
            $('#cliente-info').hide();
            $('#RFC').val('');
            $('#Direccion').val('');
            $('#TablaRem tbody tr:not(:first)').remove();
            contadorLineas = 1;
            $('#TablaRem tbody tr:first .material-no, #TablaRem tbody tr:first .material-shape').val('').trigger('change');
            
            // Restablecer fecha actual
            $('#Fecha').val(new Date().toISOString().substr(0, 10));
            
            // Restablecer ID de remisión al valor inicial
            var almacenSelect = $('#Almacen');
            var primerAlmacen = almacenSelect.find('option:first').next().val();
            var primerRecinto = almacenSelect.find('option:first').next().data('recinto');
            if (primerAlmacen && primerRecinto) {
                actualizarIdRemision(primerAlmacen, primerRecinto);
            }
            
            // Restablecer visibilidad de materiales
            verificarMateriales();
        });

        // Sincronizar los selects de material
        $(document).on('change', '.material-no', function () {
            var selectedId = $(this).val();
            $(this).closest('tr').find('.material-shape').val(selectedId).trigger('change');
        });

        $(document).on('change', '.material-shape', function () {
            var selectedId = $(this).val();
            $(this).closest('tr').find('.material-no').val(selectedId).trigger('change');
        });

        // Auto-focus en el primer campo al abrir el modal
        $('#nuevaRemision').on('shown.bs.modal', function () {
            $('#Almacen').focus();
        });

        // Limpiar validaciones cuando el usuario modifica un campo
        $(document).on('input change', 'input, select', function() {
            $(this).removeClass('is-invalid');
        });
    });

    function actualizarIdRemision(idAlmacen, recinto) {
        // Mostrar loading
        $('#IdRemision').val('Cargando...');
        
        $.ajax({
            url: 'ProcesoRemision/Obtener_consecutivo_remision.php',
            type: 'POST',
            data: {
                idAlmacen: idAlmacen,
                recinto: recinto
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#IdRemision').val(response.idRemision);
                    $('#IdRemisionEncabezado').val(response.consecutivo);
                } else {
                    Swal.fire('Error', 'No se pudo generar el ID de remisión', 'error');
                    $('#IdRemision').val('Error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión al generar ID', 'error');
                $('#IdRemision').val('Error');
            }
        });
    }

    function validarFormulario() {
        let isValid = true;
        
        // Remover clases de error previas
        $('.is-invalid').removeClass('is-invalid');
        
        // Validar campos requeridos principales
        $('#AgregarRemision [required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });

        // Verificar si debemos validar materiales (solo si la sección está visible)
        var materialesSectionVisible = $('#seccion-materiales').is(':visible');
        
        if (materialesSectionVisible) {
            // Validar que haya al menos un material
            if ($('#TablaRem tbody tr').length === 0) {
                Swal.fire('Error', 'Debe agregar al menos un material', 'error');
                isValid = false;
            }

            // Validar que todos los materiales tengan datos completos
            let materialesIncompletos = false;
            $('#TablaRem tbody tr').each(function(index) {
                const materialNo = $(this).find('[name="MaterialNo[]"]').val();
                const articulo = $(this).find('[name="Articulo[]"]').val();
                const piezas = $(this).find('[name="Piezas[]"]').val();
                const cantidad = $(this).find('[name="Cantidad[]"]').val();

                if (!materialNo || !articulo || !piezas || !cantidad) {
                    $(this).find('[required]').addClass('is-invalid');
                    materialesIncompletos = true;
                    isValid = false;
                }
            });

            if (!isValid) {
                const errores = [];
                if (!$('#Almacen').val()) errores.push('Seleccione un almacén');
                if (!$('#TipoRemision').val()) errores.push('Seleccione un tipo de remisión');
                if (!$('#Fecha').val()) errores.push('Ingrese una fecha');
                if (!$('#Cliente').val()) errores.push('Seleccione un cliente');
                if (materialesIncompletos) errores.push('Complete todos los campos de materiales');

                Swal.fire({
                    icon: 'error',
                    title: 'Formulario incompleto',
                    html: 'Por favor complete los siguientes campos:<br><br>' + 
                          errores.map(error => '• ' + error).join('<br>'),
                    confirmButtonText: 'Entendido'
                });
            }
        } else {
            // Si la sección de materiales está oculta, solo validar campos básicos
            if (!isValid) {
                const errores = [];
                if (!$('#Almacen').val()) errores.push('Seleccione un almacén');
                if (!$('#TipoRemision').val()) errores.push('Seleccione un tipo de remisión');
                if (!$('#Fecha').val()) errores.push('Ingrese una fecha');
                if (!$('#Cliente').val()) errores.push('Seleccione un cliente');

                Swal.fire({
                    icon: 'error',
                    title: 'Formulario incompleto',
                    html: 'Por favor complete los siguientes campos:<br><br>' + 
                          errores.map(error => '• ' + error).join('<br>'),
                    confirmButtonText: 'Entendido'
                });
            }
        }

        return isValid;
    }

    let contadorLineas = 1;

    function agregarFila() {
        // Verificar si la sección de materiales está visible
        if (!$('#seccion-materiales').is(':visible')) {
            Swal.fire('Advertencia', 'No se pueden agregar materiales para remisiones de salida', 'warning');
            return;
        }

        contadorLineas++;

        var nuevaFila = `
        <tr>
            <td class="text-center">
                <input type="text" class="form-control form-control-sm text-center" name="IdLinea[]" value="${contadorLineas}" readonly>
            </td>
            <td>
                <select class="form-control form-control-sm material-no" name="MaterialNo[]" required>
                    <option value="" selected>Seleccione...</option>
                    <?php foreach ($articulos as $articulo): ?>
                        <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                            <?= htmlspecialchars($articulo->MaterialNo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select class="form-control form-control-sm material-shape" name="Articulo[]" required>
                    <option value="" selected>Seleccione...</option>
                    <?php foreach ($articulos as $articulo): ?>
                        <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                            <?= htmlspecialchars($articulo->MaterialShape) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="Booking[]" 
                       onkeyup="this.value = this.value.toUpperCase()">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center" name="Piezas[]" min="0" required>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center" name="Cantidad[]" min="1" step="1" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="eliminarFila(this)" title="Eliminar">
                    <i class="fas fa-trash fa-xs"></i>
                </button>
            </td>
        </tr> `;

        $('#TablaRem tbody').append(nuevaFila);

        // Inicializar Select2 en los nuevos selects
        $('#TablaRem tbody tr:last .material-no, #TablaRem tbody tr:last .material-shape').select2({
            dropdownParent: $('#nuevaRemision'),
            width: '100%',
            dropdownAutoWidth: true
        });
    }

    function quitarUltimaFila() {
        // Verificar si la sección de materiales está visible
        if (!$('#seccion-materiales').is(':visible')) {
            Swal.fire('Advertencia', 'No se pueden modificar materiales para remisiones de salida', 'warning');
            return;
        }

        if ($('#TablaRem tbody tr').length > 1) {
            $('#TablaRem tbody tr:last').remove();
            contadorLineas--;
        } else {
            Swal.fire('Advertencia', 'Debe haber al menos un material', 'warning');
        }
    }

    function eliminarFila(btn) {
        // Verificar si la sección de materiales está visible
        if (!$('#seccion-materiales').is(':visible')) {
            Swal.fire('Advertencia', 'No se pueden eliminar materiales para remisiones de salida', 'warning');
            return;
        }

        if ($('#TablaRem tbody tr').length > 1) {
            $(btn).closest('tr').remove();
            contadorLineas--;
            
            // Renumerar las filas
            $('#TablaRem tbody tr').each(function(index) {
                $(this).find('input[name="IdLinea[]"]').val(index + 1);
            });
        } else {
            Swal.fire('Advertencia', 'Debe haber al menos un material', 'warning');
        }
    }

    // Manejar el envío del formulario con AJAX
    $(document).ready(function() {
        $('#AgregarRemision').on('submit', function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return false;
            }
            
            // Preparar datos para enviar
            const formData = new FormData(this);
            
            // Solo agregar materiales si la sección está visible
            const materialesSectionVisible = $('#seccion-materiales').is(':visible');
            
            if (materialesSectionVisible) {
                const materiales = [];
                $('#TablaRem tbody tr').each(function(index) {
                    const materialNo = $(this).find('[name="MaterialNo[]"]').val();
                    const articulo = $(this).find('[name="Articulo[]"]').val();
                    const piezas = $(this).find('[name="Piezas[]"]').val();
                    const cantidad = $(this).find('[name="Cantidad[]"]').val();
                    const booking = $(this).find('[name="Booking[]"]').val();
                    const idLinea = $(this).find('[name="IdLinea[]"]').val();
                    
                    materiales.push({
                        IdLinea: idLinea,
                        MaterialNo: materialNo,
                        Articulo: articulo,
                        Piezas: piezas,
                        Cantidad: cantidad,
                        Booking: booking
                    });
                });
                
                formData.append('Materiales', JSON.stringify(materiales));
            } else {
                // Si no hay materiales, enviar un array vacío
                formData.append('Materiales', JSON.stringify([]));
            }
            
            formData.append('Mov', 'AgregarRemision');
            
            // Mostrar loading
            Swal.fire({
                title: 'Guardando remisión...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'ProcesoRemision/GuardarRemision.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            html: `Remisión guardada exitosamente<br>
                                <strong>ID Remisión:</strong> ${response.data.idRemision}<br>
                                <strong>Tipo:</strong> ${response.data.tipoRemision}`,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#nuevaRemision').modal('hide');
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo guardar la remisión. Intente nuevamente.',
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        });
    });
</script>