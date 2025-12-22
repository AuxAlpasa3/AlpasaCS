<?php
include_once "../templates/head.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REMISIONES</title>
    
    <style>
        .table-sm td,
        .table-sm th {
            padding: 0.3rem;
            font-size: 0.85rem;
        }

        .sub-table {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .sub-table th {
            background-color: #e9ecef;
            font-size: 0.8rem;
        }

        .btn-action {
            margin: 2px;
            font-size: 0.8rem;
        }

        .info-group {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #d94f00;
            font-size: 0.75rem;
        }

        .info-value {
            color: #212529;
            font-size: 0.8rem;
        }

        .transportista-group,
        .contenedor-group {
            min-height: 80px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .spinner-fast {
            width: 3rem;
            height: 3rem;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #d94f00;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .modal-sm {
            max-width: 400px;
        }

        .btn-block {
            padding: 12px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .btn-salida-simple {
            background-color: #d94f00;
            border-color: #d94f00;
            color: white;
        }

        .btn-salida-lotes {
            background-color: #d94f00;
            border-color: #d94f00;
            color: white;
        }

        .btn-salida-simple:hover {
            background-color: #b84300;
            border-color: #b84300;
            color: white;
        }

        .btn-salida-lotes:hover {
            background-color: #b84300;
            border-color: #b84300;
            color: white;
        }

        .btn-secondary-custom {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-secondary-custom:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            color: white;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        
        .badge-custom-orange {
            background-color: #d94f00;
            color: white;
        }
        
        .dataTables_wrapper {
            position: relative;
        }
        
        .dt-buttons {
            margin-bottom: 10px;
        }
        
        .btn-export {
            margin-right: 5px;
        }
        
        .btn-detalle {
            background-color: #d94f00 !important;
            border-color: #d94f00 !important;
        }

        .close {
            color: white;
            opacity: 1;
            text-shadow: none;
        }

        .close:hover {
            color: #f0f0f0;
            opacity: 0.8;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php
        include_once "../templates/nav.php";
        include_once "../templates/aside.php";
        ?>

        <div class="content-wrapper">
            <section class="content mt-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header text-white"
                                    style="border-bottom: 2px solid #d94f00; background-color: #d94f00">
                                    <h1 class="card-title" style="font-size: 1.4rem; margin: 0;">GENERAR REMISIONES</h1>
                                </div>

                                <div class="card-body">
                                    <!-- Botones de acción -->
                                    <div class="row mb-3">
                                        <div class="col-12 text-left">
                                            <button type="button" class="btn-agregar btn btn-success btn-sm">
                                                <i class="fa fa-plus"></i> Nueva Remisión
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Loading -->
                                    <div id="loading" class="loading-overlay" style="display: none;">
                                        <div class="spinner-fast"></div>
                                        <p class="mt-2">Cargando datos...</p>
                                    </div>

                                    <!-- Tabla con Server-side processing -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm"
                                                    id="dataTableRemisiones" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="auto" style="color:black; text-align: center;">ID Remisión</th>
                                                            <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                                            <th width="auto" style="color:black; text-align: center;">Transportista</th>
                                                            <th width="auto" style="color:black; text-align: center;">Contenedor</th>
                                                            <th width="auto" style="color:black; text-align: center;">Fecha</th>
                                                            <th width="auto" style="color:black; text-align: center;">Tipo Remisión</th>
                                                            <th width="auto" style="color:black; text-align: center;">Estatus</th>
                                                            <th width="auto" style="color:black; text-align: center;">Acciones</th>
                                                            <th width="auto" style="color:black; text-align: center;">Operación</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Datos cargados via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include_once '../templates/footer.php' ?>
        <aside class="control-sidebar"></aside>
    </div>

    <div id="modal-container"></div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/jszip/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="../plugins/sweetalert2/sweetalert2.all.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function () {
            $('#loading').show();

            const table = $('#dataTableRemisiones').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ObtenerRemision.php",
                    "type": "POST",
                    "data": function (d) {
                        return d;
                    },
                    "dataSrc": function (json) {
                        $('#loading').hide();
                        
                        if (json.error) {
                            console.error('Error del servidor:', json.error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al cargar los datos: ' + json.error
                            });
                            return [];
                        }
                        
                        if (!json.data) {
                            console.error('Estructura de respuesta inválida:', json);
                            return [];
                        }
                        
                        return json.data;
                    },
                    "error": function (xhr, error, thrown) {
                        $('#loading').hide();
                        console.error('Error AJAX:', error, thrown);
                        
                        let errorMsg = 'No se pudieron cargar los datos. ';
                        
                        if (xhr.status === 0) {
                            errorMsg += 'Error de conexión. Verifique su internet.';
                        } else if (xhr.status === 500) {
                            errorMsg += 'Error interno del servidor.';
                        } else {
                            errorMsg += 'Error: ' + xhr.status + ' ' + xhr.statusText;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: errorMsg
                        });
                    }
                },
                "columns": [
                    {
                        "data": "IdRemision",
                        "className": "text-center"
                    },
                    {
                        "data": "Cliente",
                        "className": "text-center"
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return `
                                    <div class="transportista-group">
                                        <div class="info-group">
                                            <div class="info-label">Transportista:</div>
                                            <div class="info-value">${row.Transportista || 'No asignado'}</div>
                                        </div>
                                        <div class="info-group">
                                            <div class="info-label">Placas:</div>
                                            <div class="info-value">${row.Placas || 'No asignado'}</div>
                                        </div>
                                        <div class="info-group">
                                            <div class="info-label">Chofer:</div>
                                            <div class="info-value">${row.Chofer || 'No asignado'}</div>
                                        </div>
                                    </div>
                                `;
                            }
                            return row.Transportista;
                        },
                        "orderable": false
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return `
                                    <div class="contenedor-group">
                                        <div class="info-group">
                                            <div class="info-label">Contenedor:</div>
                                            <div class="info-value">${row.Contenedor || 'No asignado'}</div>
                                        </div>
                                        <div class="info-group">
                                            <div class="info-label">Tracto:</div>
                                            <div class="info-value">${row.Tracto || 'No asignado'}</div>
                                        </div>
                                        <div class="info-group">
                                            <div class="info-label">Sellos:</div>
                                            <div class="info-value">${row.Sellos || 'No asignado'}</div>
                                        </div>
                                        <div class="info-group">
                                            <div class="info-label">Caja:</div>
                                            <div class="info-value">${row.Caja || 'No asignado'}</div>
                                        </div>
                                    </div>
                                `;
                            }
                            return row.Contenedor;
                        },
                        "orderable": false
                    },
                    {
                        "data": "FechaRemision",
                        "className": "text-center",
                        "render": function (data) {
                            return data ? new Date(data).toLocaleDateString('es-MX') : '';
                        }
                    },
                    {
                        "data": "TipoRemision",
                        "className": "text-center"
                    },
                    {
                        "data": "Estatus",
                        "render": function (data, type, row) {
                            let badgeClass = row.BadgeClass || 'badge-secondary';
                            return `<span class="badge ${badgeClass}">${data || 'Sin estatus'}</span>`;
                        },
                        "className": "text-center"
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return `
                                <div style="text-align: center;">
                                    <button type="button" class="btn-detalle btn btn-sm btn-action" 
                                            data-id="${row.IdRemision}" 
                                            data-idalmacen="${row.IdAlmacen}"
                                            data-idencabezado="${row.IdRemisionEncabezado}"
                                            title="Ver detalle de remisión">
                                        <i class="fa fa-eye" style="color: white;"></i>
                                    </button>
                                    <button type="button" class="btn-editar btn btn-warning btn-sm btn-action" 
                                            data-id="${row.IdRemisionEncabezado}" 
                                            title="Editar remisión">
                                        <i class="fa fa-pen"  style="color: white;"></i>
                                    </button>
                                    <button type="button" class="btn-eliminar btn btn-danger btn-sm btn-action" 
                                            data-id="${row.IdRemision}" 
                                            data-idalmacen="${row.IdAlmacen}"
                                            data-idencabezado="${row.IdRemisionEncabezado}"
                                            title="Eliminar remisión">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        },
                        "orderable": false,
                        "className": "text-center"
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            let operacionHTML = '';
                            
                            if (row.TipoRemisionNum == 1) {
                                const disabled = row.Disabled || '';
                                const title = row.Title || 'Habilitar para ingresar';
                                
                                operacionHTML = `
                                    <div style="text-align: center;">
                                        <button type="button" class="btn btn-success btn-sm btn-ingresar" 
                                                data-id="${row.IdRemisionEncabezado}" 
                                                data-idalmacen="${row.IdAlmacen}" 
                                                ${disabled} 
                                                title="${title}">
                                            INGRESAR
                                        </button>
                                    </div>
                                `;
                            } else if (row.TipoRemisionNum == 2) {
                                const disabled = (row.TotalLineasSinPiezas != 0) ? 'disabled' : '';
                                const title = (disabled) ? 'Complete el número de piezas para habilitar el botón' : 'Dar salida a la remisión';

                                operacionHTML = `
                                    <div style="text-align: center;">
                                        <button type="button" class="btn btn-danger btn-sm btn-salida-modal" 
                                                data-id="${row.IdRemisionEncabezado}" 
                                                data-idalmacen="${row.IdAlmacen}"
                                                data-idremision="${row.IdRemision}"
                                                data-user="<?= $IdUsuario ?>"
                                                ${disabled} 
                                                title="${title}">
                                            DAR SALIDA
                                        </button>
                                    </div>
                                `;
                            } else {
                                operacionHTML = '<div class="text-center">-</div>';
                            }
                            
                            return operacionHTML;
                        },
                        "orderable": false,
                        "className": "text-center"
                    }
                ],
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar a Excel',
                        className: 'btn btn-success btn-sm btn-export',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Exportar a PDF',
                        className: 'btn btn-danger btn-sm btn-export',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            doc.defaultStyle.fontSize = 7;
                            doc.styles.tableHeader.fontSize = 8;
                            doc.pageMargins = [10, 10, 10, 10];
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Imprimir',
                        className: 'btn btn-info btn-sm btn-export',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                "initComplete": function () {
                    $('#loading').hide();
                },
                "drawCallback": function () {
                    reinicializarEventos();
                }
            });

            function reinicializarEventos() {
                $(document).off('click', '.btn-detalle').on('click', '.btn-detalle', function() {
                    var idRemision = $(this).data('id');
                    var idAlmacen = $(this).data('idalmacen');
                    var idEncabezado = $(this).data('idencabezado');
                    irADetalle(idRemision, idAlmacen, idEncabezado);
                });

                $(document).off('click', '.btn-editar').on('click', '.btn-editar', function() {
                    var id = $(this).data('id');
                    cargarModalEditar(id);
                });

                $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
                    var idRemision = $(this).data('id');
                    var idAlmacen = $(this).data('idalmacen');
                    var idEncabezado = $(this).data('idencabezado');
                    cargarModalEliminar(idRemision, idAlmacen, idEncabezado);
                });

                $(document).off('click', '.btn-ingresar').on('click', '.btn-ingresar', function(e) {
                    e.preventDefault();
                    var IdRemisionEncabezado = $(this).data('id');
                    var idAlmacen = $(this).data('idalmacen');
                    confirmarIngreso(IdRemisionEncabezado, idAlmacen);
                });

                $(document).off('click', '.btn-salida-modal').on('click', '.btn-salida-modal', function() {
                    var idEncabezado = $(this).data('id');
                    var idAlmacen = $(this).data('idalmacen');
                    var idRemision = $(this).data('idremision');
                    var userId = $(this).data('user');
                    mostrarModalSalida(idEncabezado, idAlmacen, idRemision, userId);
                });

                $(document).off('click', '.btn-agregar').on('click', '.btn-agregar', function() {
                    cargarModalAgregar();
                });
            }

            function irADetalle(idRemision, idAlmacen, idEncabezado) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'RemisionDetalle.php';
                
                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = idRemision;
                form.appendChild(inputId);
                
                var inputAlmacen = document.createElement('input');
                inputAlmacen.type = 'hidden';
                inputAlmacen.name = 'idAlmacen';
                inputAlmacen.value = idAlmacen;
                form.appendChild(inputAlmacen);
                
                var inputEncabezado = document.createElement('input');
                inputEncabezado.type = 'hidden';
                inputEncabezado.name = 'idEncabezado';
                inputEncabezado.value = idEncabezado;
                form.appendChild(inputEncabezado);
                
                document.body.appendChild(form);
                form.submit();
            }

            function cargarModalAgregar() {
                $('#modal-container').load('ProcesoRemision/Agregar.php', function (response, status, xhr) {
                    if (status === "error") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo cargar el modal de agregar remisión',
                            showConfirmButton: true
                        });
                        return;
                    }
                    
                    $('#nuevaRemision').modal('show');
                    
                    $(document).off('click.modal-close').on('click.modal-close',
                        '[data-dismiss="modal"], .btn-close, .modal-close',
                        function () {
                            $('#nuevaRemision').modal('hide');
                            table.ajax.reload();
                        }
                    );
                });
            }

            function cargarModalEditar(id) {
                $('#modal-container').load('ProcesoRemision/Modificar.php?IdRemisionEncabezado=' + id,
                    function (response, status, xhr) {
                        if (status === "error") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo cargar el modal de edición',
                                showConfirmButton: true
                            });
                            return;
                        }
                        
                        $('#ModificarRemision').modal('show');
                        
                        $(document).off('click.modal-close').on('click.modal-close',
                            '[data-dismiss="modal"], .btn-close, .modal-close',
                            function () {
                                $('#ModificarRemision').modal('hide');
                                table.ajax.reload();
                            }
                        );
                    });
            }

            function cargarModalEliminar(idRemision,idAlmacen,idEncabezado) {
                $('#modal-container').load('ProcesoRemision/Eliminar.php?IdRemisionEncabezado=' + idEncabezado
                + '&IdRemision=' + idRemision + '&IdAlmacen=' + idAlmacen,
                    function(response, status, xhr) {
                        if (status === "error") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo cargar el modal de eliminación',
                                showConfirmButton: true
                            });
                            return;
                        }
                        
                        $('#EliminarRemision').modal('show');
                        
                        $(document).off('click.modal-close-eliminar').on('click.modal-close-eliminar',
                            '[data-dismiss="modal"], .btn-close, .modal-close',
                            function() {
                                $('#EliminarRemision').modal('hide');
                                table.ajax.reload();
                            }
                        );
                    });
            }

            function confirmarIngreso(IdRemisionEncabezado, idAlmacen) {
                var formData = {
                    id: IdRemisionEncabezado,
                    user: <?= $IdUsuario ?>,
                    Almacen: idAlmacen,
                    mov: 'INGRESAR'
                };

                Swal.fire({
                    title: '¿Confirmar ingreso?',
                    text: "¿Estás seguro de que deseas ingresar la Remisión #" + IdRemisionEncabezado + "?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d94f00',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, ingresar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarIngreso(formData);
                    }
                });
            }

            function enviarIngreso(formData) {
                $.ajax({
                    url: 'RemisionIngreso.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function () {
                        Swal.fire({
                            title: 'Procesando...',
                            html: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function (response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'Ocurrió un error al procesar la solicitud: ' + error,
                            showConfirmButton: true
                        });
                    }
                });
            }

            function mostrarModalSalida(idEncabezado, idAlmacen, idRemision, userId) {
                const modalHTML = `
                    <div class="modal fade" id="modalTipoSalida" tabindex="-1" role="dialog" aria-labelledby="modalTipoSalidaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #d94f00; color: white;">
                                    <h5 class="modal-title" id="modalTipoSalidaLabel">Tipo de Salida</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" style="color: white;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Seleccione el tipo de salida para la remisión:</p>
                                    
                                    <div class="row mt-4">
                                        <div class="col-12 mb-3">
                                            <button type="button" class="btn btn-salida-simple btn-block" 
                                                    data-tipo="simple">
                                                <i class="fa fa-box"></i> Salida Simple
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-salida-lotes btn-block" 
                                                    data-tipo="lotes">
                                                <i class="fa fa-boxes"></i> Salida por Lotes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary-custom" data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#modal-container').html(modalHTML);
                $('#modalTipoSalida').modal('show');
                
                // Evento para el botón de cerrar (X)
                $('#modalTipoSalida .close').off('click').on('click', function() {
                    $('#modalTipoSalida').modal('hide');
                });
                
                // Evento para el botón Cancelar
                $('#modalTipoSalida .btn-secondary-custom').off('click').on('click', function() {
                    $('#modalTipoSalida').modal('hide');
                });
                
                $('.btn-salida-simple').off('click').on('click', function() {
                    procesarSalida('simple', idEncabezado, idAlmacen, idRemision, userId);
                });
                
                $('.btn-salida-lotes').off('click').on('click', function() {
                    procesarSalida('lotes', idEncabezado, idAlmacen, idRemision, userId);
                });
                
                $('#modalTipoSalida').on('hidden.bs.modal', function() {
                    $(this).remove();
                    $('#modal-container').empty();
                });
            }

            function procesarSalida(tipo, idEncabezado, idAlmacen, idRemision, userId) {
                $('#modalTipoSalida').modal('hide');
                
                let actionUrl = '';
                if (tipo === 'simple') {
                    actionUrl = (idAlmacen == 40) ? 'RemisionSalidaRoyal.php' : 'RemisionSalida.php';
                } else {
                    actionUrl = (idAlmacen == 40) ? 'RemisionSalidaLotesRoyal.php' : 'RemisionSalidaLotes.php';
                }
                
                Swal.fire({
                    title: `Confirmar Salida ${tipo === 'simple' ? 'Simple' : 'por Lotes'}`,
                    text: `¿Está seguro de proceder con la salida ${tipo === 'simple' ? 'simple' : 'por lotes'}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d94f00',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = actionUrl;
                        
                        const campos = [
                            { name: 'id', value: idEncabezado },
                            { name: 'Almacen', value: idAlmacen },
                            { name: 'user', value: userId },
                            { name: 'idRemisionEncabezado', value: idEncabezado },
                            { name: 'idRemision', value: idRemision },
                            { name: 'tipoSalida', value: tipo }
                        ];
                        
                        campos.forEach(campo => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = campo.name;
                            input.value = campo.value;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            reinicializarEventos();

            $(document).on('hidden.bs.modal', '.modal', function () {
                $(this).remove();
                $('#modal-container').empty();
            });
        });
    </script>
</body>
</html>