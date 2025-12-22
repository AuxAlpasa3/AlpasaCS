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

<div class="modal fade" id="eliminarDetalle" tabindex="-1" role="dialog" aria-labelledby="eliminarDetalleTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="eliminarDetalleTitle">
                    Eliminar Detalle de Remisión: <b><?php echo $IdRemision; ?></b>
                </h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEliminarDetalle" name="eliminarDetalle" method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">
                        <div class="col-md-12" style="text-align: center;">
                            <label for="Id">¿Estás seguro de eliminar la linea <?php echo $IdLinea; ?> del
                                detalle de la
                                remisión?</label>
                            <div class="form-group">
                                <div class="row">
                                    <input type="hidden" id="IdUsuario" name="IdUsuario"
                                        value="<?php echo $IdUsuario; ?>">
                                    <input id="IdLinea" class="form-control" type="text" name="IdLinea"
                                        value="<?php echo $IdLinea; ?>" hidden>
                                    <input type="hidden" id="IdRemision" name="IdRemision"
                                        value="<?php echo $IdRemision; ?>">
                                    <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado"
                                        value="<?php echo $IdRemisionEncabezado; ?>">
                                    <input type="hidden" id="IdAlmacen" name="IdAlmacen"
                                        value="<?php echo $IdAlmacen; ?>">
                                    <input type="hidden" id="IdArticulo" name="IdArticulo"
                                        value="<?php echo $IdArticulo; ?>">
                                    <input type="hidden" id="Cliente" name="Cliente" value="<?php echo $Cliente; ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group text-center">
                                        <label for="IdRemision">IdRemision</label>
                                        <input id="IdRemision" class="form-control" type="text" name="IdRemision"
                                            value="<?php echo $IdRemision; ?>" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group text-center">
                                        <label for="Comentarios">Articulo</label>
                                        <input id="Articulo" class="form-control" type="text" name="Articulo"
                                            value="<?php echo htmlspecialchars($articuloQuery->MaterialShape); ?>"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group text-center">
                                        <label for="Comentarios">Comentarios</label>
                                        <textarea id="Comentarios" class="form-control" name="Comentarios" rows="2"
                                            cols="10" placeholder="Escribe aquí..."></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="button" onclick="eliminarDetalleRemi()">
                                    Si
                                </button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">
                                    No
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        inicializarEventosModalEliminar();
    });

    function inicializarEventosModalEliminar() {
        $('#eliminarDetalle').on('hidden.bs.modal', function () { });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('#eliminarDetalle').is(':visible')) {
                $('#eliminarDetalle').modal('hide');
            }

            if (e.key === 'Enter' && $('#eliminarDetalle').is(':visible')) {
                e.preventDefault();
                eliminarDetalleRemi();
            }
        });

        $('#formEliminarDetalle').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                eliminarDetalleRemi();
            }
        });
    }

    function eliminarDetalleRemi() {
        Swal.fire({
            title: '¿Está completamente seguro?',
            text: "¡Esta acción no se puede revertir!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                procederEliminacion();
            }
        });
    }

    function prepararDatosFormularioEliminar() {
        return {
            Comentarios: $('#Comentarios').val()
        };
    }



    function procederEliminacion() {


        detalleData = prepararDatosFormularioEliminar();
        Swal.fire({
            title: 'Eliminando detalle...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('Mov', 'eliminarDetalle');
        formData.append('IdRemision', $('#IdRemision').val());
        formData.append('IdRemisionEncabezado', $('#IdRemisionEncabezado').val());
        formData.append('Cliente', $('#Cliente').val());
        formData.append('IdUsuario', $('#IdUsuario').val());
        formData.append('IdLinea', $('#IdLinea').val());
        formData.append('IdArticulo', $('#IdArticulo').val());
        formData.append('IdAlmacen', $('#IdAlmacen').val());
        formData.append('Comentarios', detalleData.Comentarios);

        $.ajax({
            url: 'ProcesoRemDetalle/EliminarDetalleRemision.php',
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
                        title: '¡Detalle eliminado!',
                        text: 'El detalle se ha eliminado exitosamente',
                        confirmButtonText: 'Aceptar',
                        timer: 2000,
                        timerProgressBar: true
                    }).then((result) => {
                        $('#eliminarDetalle').modal('hide');

                        if (typeof table !== 'undefined' && typeof table.ajax !== 'undefined') {
                            table.ajax.reload(null, false);
                        } else if (typeof actualizarTablaDetalles === 'function') {
                            actualizarTablaDetalles();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    mostrarError('Error al eliminar: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                Swal.close();
                mostrarError('Error de conexión: ' + error);
                console.error('Error completo:', xhr.responseText);
            }
        });
    }

    function mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: mensaje,
            confirmButtonText: 'Entendido'
        });
    }

    $(document).on('submit', '#formEliminarDetalle', function (e) {
        e.preventDefault();
        eliminarDetalleRemi();
    });
</script>