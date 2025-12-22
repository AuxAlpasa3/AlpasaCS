<?php
include_once "../../templates/Sesion.php";

$IdRemision = isset($_GET["IdRemision"]) ? htmlspecialchars($_GET["IdRemision"]) : '';
$IdRemisionEncabezado = isset($_GET["IdRemisionEncabezado"]) ? intval($_GET["IdRemisionEncabezado"]) : 0;
$IdAlmacen = isset($_GET["IdAlmacen"]) ? intval($_GET["IdAlmacen"]) : 0;

if (empty($IdRemision) || $IdRemisionEncabezado <= 0) {
    die("Parámetros inválidos");
}

try {
    $articulos = $Conexion->query("SELECT DISTINCT(t1.IdArticulo), t1.MaterialNo, CONCAT(t1.Material,' ',t1.Shape) AS MaterialShape 
                                  FROM t_articulo AS t1 
                                  INNER JOIN t_articulo_almacen as t2 on t1.IdArticulo = t2.IdArticulo
                                  INNER JOIN t_usuario_almacen as t3 on t2.IdAlmacen = t3.IdAlmacen
                                  WHERE t3.IdUsuario = $IdUsuario")->fetchAll(PDO::FETCH_OBJ);

    $result = $Conexion->query("SELECT COALESCE(MAX(idlinea), 0) + 1 as IdLinea 
                               FROM t_remision_linea 
                               WHERE IdRemisionEncabezadoRef = $IdRemisionEncabezado 
                               AND IdRemision = '$IdRemision'")->fetch(PDO::FETCH_OBJ);
    $IdLinea = $result->IdLinea;

    $result2 = $Conexion->query("SELECT distinct(Cliente) 
                               FROM t_remision_linea 
                               WHERE IdRemisionEncabezadoRef = $IdRemisionEncabezado 
                               AND IdRemision = '$IdRemision'")->fetch(PDO::FETCH_OBJ);
    $Cliente = $result2->Cliente;

} catch (Exception $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<div class="modal fade" id="nuevoDetalle" tabindex="-1" role="dialog" aria-labelledby="nuevoDetalleTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="nuevoDetalleTitle">Nuevo Detalle de Remisión:
                    <b><?php echo $IdRemision; ?></b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="nuevoDetalle" name="nuevoDetalle" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="IdRemision" name="IdRemision" value="<?php echo $IdRemision; ?>">
                    <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado"
                        value="<?php echo $IdRemisionEncabezado; ?>">

                    <input type="hidden" id="Cliente" name="Cliente" value="<?php echo $Cliente; ?>">
                    <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?php echo $IdUsuario; ?>">
                    <input type="hidden" id="IdLinea" name="IdLinea" value="<?php echo $IdLinea; ?>">
                    <input type="hidden" id="IdAlmacen" name="IdAlmacen" value="<?php echo $IdAlmacen; ?>">

                    <div class="step" id="step1">
                        <div class="row">
                            <div class="col-md-2 form-group text-center">
                                <label for="IdLineaDisplay">ID Línea</label>
                                <input type="text" class="form-control" id="IdLineaDisplay"
                                    value="<?php echo $IdLinea; ?>" readonly>
                            </div>
                            <div class="col-md-5 form-group text-center">
                                <label for="MaterialNo">Material No.</label>
                                <select class="form-control select2" name="MaterialNo" id="MaterialNo" required>
                                    <option value="" disabled selected>Seleccione el Material No...</option>
                                    <?php foreach ($articulos as $articulo): ?>
                                        <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                                            <?= htmlspecialchars($articulo->MaterialNo) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5 form-group text-center">
                                <label for="Articulo">Artículo</label>
                                <select class="form-control select2" name="Articulo" id="Articulo" required>
                                    <option value="" disabled selected>Seleccione el Artículo...</option>
                                    <?php foreach ($articulos as $articulo): ?>
                                        <option value="<?= htmlspecialchars($articulo->IdArticulo) ?>">
                                            <?= htmlspecialchars($articulo->MaterialShape) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group text-center">
                                <label for="Booking">Booking</label>
                                <input type="text" class="form-control" id="Booking" name="Booking" placeholder="Referencia">
                            </div>
                            <div class="col-md-4 form-group text-center">
                                <label for="Piezas">Piezas</label>
                                <input type="number" class="form-control" id="Piezas" name="Piezas" min="1" value="1"
                                    step="1" required>
                            </div>
                            <div class="col-md-4 form-group text-center">
                                <label for="Cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="Cantidad" name="Cantidad" value="1"
                                    min="1" step="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group text-center">
                                <label for="Comentarios">Comentarios</label>
                                <textarea id="Comentarios" class="form-control" name="Comentarios" rows="2" cols="10"
                                    placeholder="Escribe aquí..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="btnGuardarDetalle"
                                onclick="guardarDetalle()">
                                <i class="fas fa-save"></i> Guardar Detalle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let detalleData = {};

    $(document).ready(function () {
        $('.select2').select2({
            dropdownParent: $('#nuevoDetalle .modal-content'),
            width: '100%',
            placeholder: "Seleccione una opción",
            allowClear: true,
            language: {
                noResults: function () {
                    return "No se encontraron resultados";
                }
            }
        });

        $(document).on('change', '#MaterialNo', function () {
            var selectedId = $(this).val();
            $('#Articulo').val(selectedId).trigger('change');
            actualizarDatosMaterial(selectedId);
        });

        $(document).on('change', '#Articulo', function () {
            var selectedId = $(this).val();
            $('#MaterialNo').val(selectedId).trigger('change');
            actualizarDatosMaterial(selectedId);
        });

        $('#nuevoDetalle').on('hidden.bs.modal', function () {
            resetForm();
        });

        $('#nuevoDetalle').on('shown.bs.modal', function () {
            $('.select2').val(null).trigger('change');
            $('#Piezas').val(0);
            $('#Cantidad').val('');
            $('#Observaciones').val('');
            $('#Prioridad').val('Normal');
        });

        $('#nuevoDetalle input, #nuevoDetalle select, #nuevoDetalle textarea').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                guardarDetalle();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('#nuevoDetalle').is(':visible')) {
                $('#nuevoDetalle').modal('hide');
            }
        });

        $('#nuevoDetalle').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                guardarDetalle();
            }
        });
    });

    function validarFormulario() {
        let isValid = true;
        let errorMessages = [];

        $('.is-invalid').removeClass('is-invalid');

        if (!$('#MaterialNo').val()) {
            $('#MaterialNo').addClass('is-invalid');
            errorMessages.push('Debe seleccionar un Material No');
            isValid = false;
        }

        if (!$('#Articulo').val()) {
            $('#Articulo').addClass('is-invalid');
            errorMessages.push('Debe seleccionar un Artículo');
            isValid = false;
        }

        const piezas = $('#Piezas').val();
        if (!piezas || piezas < 0) {
            $('#Piezas').addClass('is-invalid');
            errorMessages.push('Las piezas deben ser un número mayor o igual a 0');
            isValid = false;
        }

        const cantidad = $('#Cantidad').val();
        if (!cantidad || cantidad < 1) {
            $('#Cantidad').addClass('is-invalid');
            errorMessages.push('La cantidad debe ser un número mayor a 0');
            isValid = false;
        }

        if (!isValid) {
            mostrarError('Errores en el formulario:<br>' + errorMessages.join('<br>'));
        }

        return isValid;
    }

    function prepararDatosFormulario() {
        return {
            MaterialNo: $('#MaterialNo').val(),
            Articulo: $('#Articulo').val(),
            Piezas: $('#Piezas').val(),
            Cantidad: $('#Cantidad').val(),
            Comentarios: $('#Comentarios').val()
        };
    }

    function guardarDetalle() {
        if (!validarFormulario()) {
            return;
        }

        detalleData = prepararDatosFormulario();

        Swal.fire({
            title: 'Guardando detalle...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('Mov', 'nuevoDetalle');
        formData.append('IdRemision', $('#IdRemision').val());
        formData.append('IdRemisionEncabezado', $('#IdRemisionEncabezado').val());
        formData.append('Cliente', $('#Cliente').val());
        formData.append('IdUsuario', $('#IdUsuario').val());
        formData.append('IdLinea', $('#IdLinea').val());
        formData.append('MaterialNo', detalleData.MaterialNo);
        formData.append('Articulo', detalleData.Articulo);
        formData.append('Piezas', detalleData.Piezas);
        formData.append('Booking',  $('#Booking').val());
        formData.append('Cantidad', detalleData.Cantidad);
        formData.append('Comentarios', detalleData.Comentarios);
        formData.append('IdAlmacen', $('#IdAlmacen').val());

        $.ajax({
            url: 'ProcesoRemDetalle/GuardarDetalleRemision.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                Swal.close();

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Detalle guardado!',
                        text: 'El detalle se ha agregado exitosamente',
                        confirmButtonText: 'Aceptar',
                        timer: 2000,
                        timerProgressBar: true
                    }).then((result) => {
                        $('#nuevoDetalle').modal('hide');

                        if (typeof actualizarTablaDetalles === 'function') {
                            actualizarTablaDetalles();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    mostrarError('Error al guardar: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                Swal.close();
                mostrarError('Error de conexión: ' + error);

                console.error('Error completo:', xhr.responseText);
            }
        });
    }

    function resetForm() {
        $('#nuevoDetalle')[0].reset();
        $('.select2').val(null).trigger('change');
        $('.is-invalid').removeClass('is-invalid');
        detalleData = {};
    }

    function mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: mensaje,
            confirmButtonText: 'Entendido',
            customClass: {
                popup: 'animated bounceIn'
            }
        });
    }

    function mostrarExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: mensaje,
            confirmButtonText: 'Aceptar',
            timerProgressBar: true
        });
    }

    $(document).on('submit', '#nuevoDetalle', function (e) {
        e.preventDefault();
        guardarDetalle();
    });
</script>