<?php
include_once "../../templates/Sesion.php";

$IdRemision = isset($_GET["IdRemision"]) ? htmlspecialchars($_GET["IdRemision"]) : '';
$IdRemisionEncabezado = isset($_GET["IdRemisionEncabezado"]) ? intval($_GET["IdRemisionEncabezado"]) : 0;
$IdAlmacen = isset($_GET["IdAlmacen"]) ? intval($_GET["IdAlmacen"]) : 0;
$IdLinea = isset($_GET["IdLinea"]) ? intval($_GET["IdLinea"]) : 0;
$IdArticulo = isset($_GET["IdArticulo"]) ? intval($_GET["IdArticulo"]) : 0;

if (empty($IdRemision) || $IdRemisionEncabezado <= 0 || $IdLinea <= 0 || $IdArticulo <= 0) {
    die("Parámetros inválidos");
}

try {
    $detalleQuery = $Conexion->query("SELECT * FROM t_remision_linea 
                                     WHERE IdRemisionEncabezadoRef = $IdRemisionEncabezado 
                                     AND IdRemision = '$IdRemision' 
                                     AND IdLinea = $IdLinea")->fetch(PDO::FETCH_OBJ);

    if (!$detalleQuery) {
        die("Detalle no encontrado");
    }

    $articuloQuery = $Conexion->query("SELECT MaterialNo, CONCAT(Material,' ',Shape) AS MaterialShape 
                                      FROM t_articulo 
                                      WHERE IdArticulo = $IdArticulo")->fetch(PDO::FETCH_OBJ);

    $result2 = $Conexion->query("SELECT distinct(Cliente) 
                               FROM t_remision_linea 
                               WHERE IdRemisionEncabezadoRef = $IdRemisionEncabezado 
                               AND IdRemision = '$IdRemision'")->fetch(PDO::FETCH_OBJ);
    $Cliente = $result2->Cliente;

} catch (Exception $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<div class="modal fade" id="modificarDetalle" tabindex="-1" role="dialog" aria-labelledby="modificarDetalleTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="modificarDetalleTitle">Modificar Detalle de Remisión:
                    <b><?php echo $IdRemision; ?></b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formModificarDetalle" name="modificarDetalle" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="IdRemision" name="IdRemision" value="<?php echo $IdRemision; ?>">
                    <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado"
                        value="<?php echo $IdRemisionEncabezado; ?>">
                    <input type="hidden" id="Cliente" name="Cliente" value="<?php echo $Cliente; ?>">
                    <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?php echo $IdUsuario; ?>">
                    <input type="hidden" id="IdLinea" name="IdLinea" value="<?php echo $IdLinea; ?>">
                    <input type="hidden" id="IdAlmacen" name="IdAlmacen" value="<?php echo $IdAlmacen; ?>">
                    <input type="hidden" id="IdArticulo" name="IdArticulo" value="<?php echo $IdArticulo; ?>">

                    <div class="row">
                        <div class="col-md-4 form-group text-center">
                            <label for="IdLineaDisplay">ID Línea</label>
                            <input type="text" class="form-control" id="IdLineaDisplay" value="<?php echo $IdLinea; ?>"
                                readonly>
                        </div>
                        <div class="col-md-4 form-group text-center">
                            <label for="Articulo">Artículo</label>
                            <input type="text" class="form-control" id="Articulo"
                                value="<?php echo htmlspecialchars($articuloQuery->MaterialShape); ?>" readonly>
                        </div>
                        <div class="col-md-4 form-group text-center">
                            <label for="MaterialNo">Material No.</label>
                            <input type="text" class="form-control" id="MaterialNo"
                                value="<?php echo htmlspecialchars($articuloQuery->MaterialNo); ?>" readonly>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group text-center">
                            <label for="Booking">Booking</label>
                            <input type="text" class="form-control" id="Booking" name="Booking"
                                value="<?php echo $detalleQuery->Booking; ?>" >
                        </div>
                        <div class="col-md-6 form-group text-center">
                            <label for="Piezas">Piezas</label>
                            <input type="number" class="form-control" id="Piezas" name="Piezas"
                                value="<?php echo $detalleQuery->Piezas; ?>" min="1" step="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group text-center">
                            <label for="Comentarios">Comentarios</label>
                            <textarea id="Comentarios" class="form-control" name="Comentarios" rows="2" cols="10"
                                placeholder="Escribe aquí..."><?php echo htmlspecialchars($detalleQuery->Comentarios ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="modificarDetalleRemi()">
                            <i class="fas fa-edit"></i> Modificar Detalle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let detalleData = {};

    $(document).ready(function () {
        inicializarEventosModal();
    });

    function inicializarEventosModal() {
        $('#modificarDetalle').on('hidden.bs.modal', function () {
            resetForm();
        });

        $('#formModificarDetalle input, #formModificarDetalle textarea').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                modificarDetalleRemi();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('#modificarDetalle').is(':visible')) {
                $('#modificarDetalle').modal('hide');
            }
        });

        $('#formModificarDetalle').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                modificarDetalleRemi();
            }
        });
    }

    function validarFormularioModificar() {
        let isValid = true;
        let errorMessages = [];

        $('.is-invalid').removeClass('is-invalid');

        const piezas = $('#Piezas').val();
        const piezasNum = parseInt(piezas);

        if (!piezas || isNaN(piezasNum)) {
            $('#Piezas').addClass('is-invalid');
            errorMessages.push('Las piezas deben ser un número válido');
            isValid = false;
        } else if (piezasNum < 1) {
            $('#Piezas').addClass('is-invalid');
            errorMessages.push('Las piezas deben ser un número mayor a 0');
            isValid = false;
        }

        if (!isValid) {
            mostrarError('Errores en el formulario:<br>' + errorMessages.join('<br>'));
        }

        return isValid;
    }

    function prepararDatosFormularioModificar() {
        return {
            Piezas: $('#Piezas').val(),
            Comentarios: $('#Comentarios').val()
        };
    }

    function modificarDetalleRemi() {
        if (!validarFormularioModificar()) {
            return;
        }

        detalleData = prepararDatosFormularioModificar();

        Swal.fire({
            title: 'Modificando detalle...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('Mov', 'modificarDetalle');
        formData.append('IdRemision', $('#IdRemision').val());
        formData.append('IdRemisionEncabezado', $('#IdRemisionEncabezado').val());
        formData.append('Cliente', $('#Cliente').val());
        formData.append('IdUsuario', $('#IdUsuario').val());
        formData.append('IdLinea', $('#IdLinea').val());
        formData.append('IdArticulo', $('#IdArticulo').val()); 
        formData.append('Booking', $('#Booking').val());
        formData.append('Piezas', detalleData.Piezas);
        formData.append('Comentarios', detalleData.Comentarios);
        formData.append('IdAlmacen', $('#IdAlmacen').val());

        $.ajax({
            url: 'ProcesoRemDetalle/ModificarDetalleRemision.php',
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
                        title: '¡Detalle modificado!',
                        text: 'El detalle se ha actualizado exitosamente',
                        confirmButtonText: 'Aceptar',
                        timer: 2000,
                        timerProgressBar: true
                    }).then((result) => {
                        $('#modificarDetalle').modal('hide');

                        if (typeof table !== 'undefined' && typeof table.ajax !== 'undefined') {
                            table.ajax.reload(null, false);
                        } else if (typeof actualizarTablaDetalles === 'function') {
                            actualizarTablaDetalles();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    mostrarError('Error al modificar: ' + response.message);
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
        $('#formModificarDetalle')[0].reset();
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

    $(document).on('submit', '#formModificarDetalle', function (e) {
        e.preventDefault();
        modificarDetalleRemi();
    });
</script>