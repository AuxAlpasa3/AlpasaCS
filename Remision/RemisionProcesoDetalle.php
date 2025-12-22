<?php
include_once "../templates/head.php";

$IdRemisionEncabezado = isset($_POST['idEncabezado']) ? $_POST['idEncabezado'] : (isset($_POST['IdRemisionEncabezado']) ? $_POST['IdRemisionEncabezado'] : '');
$IdRemision = isset($_POST['id']) ? $_POST['id'] : (isset($_POST['IdRemision']) ? $_POST['IdRemision'] : '');
$IdAlmacen = isset($_POST['idAlmacen']) ? $_POST['idAlmacen'] : (isset($_POST['idAlmacen']) ? $_POST['idAlmacen'] : '');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>REMISION DETALLE</title>
  <style>
    .table-sm td,
    .table-sm th {
      padding: 0.3rem;
      font-size: 0.85rem;
    }

    .btn-action {
      margin: 2px;
      font-size: 0.8rem;
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

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .btn-historicos {
      background-color: #6c757d;
      border-color: #6c757d;
      color: white;
    }

    .btn-historicos:hover {
      background-color: #5a6268;
      border-color: #545b62;
      color: white;
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
                <div class="card-header text-white" style="border-bottom: 2px solid #d94f00; background-color: #d94f00">
                  <h1 class="card-title" style="font-size: 1.4rem; margin: 0;">REMISION DETALLE:
                    <b><?php echo htmlspecialchars($IdRemision); ?></b>
                  </h1>
                </div>

                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-12 text-left">
                      <button type="button" class="btn-historicos btn btn-historicos btn-sm">
                        <i class="fa fa-history"></i> Movimientos Históricos
                      </button>
                    </div>
                  </div>

                  <div id="loading" style="display: none; text-align: center; padding: 20px;">
                    <div class="spinner-fast"></div>
                    <p class="mt-2">Cargando datos...</p>
                  </div>

                  <div class="row mt-3">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="dataTableDetalle"
                          style="width:100%">
                          <thead>
                            <tr>
                              <th width="auto" style="color:black; text-align: center;">
                                IdLinea</th>
                              <th width="auto" style="color:black; text-align: center;">
                                Material No.</th>
                              <th width="auto" style="color:black; text-align: center;">
                                Articulo</th>
                              <th width="auto" style="color:black; text-align: center;">
                                Piezas</th>
                              <th width="auto" style="color:black; text-align: center;">
                                Booking</th>
                            </tr>
                          </thead>
                          <tbody>
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
      const table = $('#dataTableDetalle').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": "ProcesoRemDetalle/ObtenerRemisionProcesoDetalle.php",
          "type": "POST",
          "data": function (d) {
            d.IdRemision = '<?php echo $IdRemision; ?>';
            d.IdRemisionEncabezado = '<?php echo $IdRemisionEncabezado; ?>';
            d.IdAlmacen = '<?php echo $IdAlmacen; ?>';
          },
          "dataSrc": function (json) {
            if (json.recordsTotal === 0) {
              $('#dataTableDetalle tbody').html(
                '<tr><td colspan="5" class="text-center">No hay detalles de remisión disponibles</td></tr>'
              );
            }
            return json.data;
          }
        },
        "columns": [{
          "data": "IdLinea",
          "className": "text-center"
        },
        {
          "data": "MaterialNo",
          "className": "text-center"
        },
        {
          "data": "Articulo",
          "className": "text-center"
        },
        {
          "data": "Piezas",
          "className": "text-center"
        },
        {
          "data": "Booking",
          "className": "text-center"
        },
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
        "buttons": [{
          extend: 'excelHtml5',
          text: 'Exportar a Excel',
          className: 'btn btn-success btn-sm',
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'pdfHtml5',
          text: 'Exportar a PDF',
          className: 'btn btn-danger btn-sm',
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
          className: 'btn btn-info btn-sm',
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
        $(document).off('click', '.btn-historicos').on('click', '.btn-historicos', function () {
          cargarModalHistoricos();
        });
      }

      function cargarModalHistoricos() {
        $('#modal-container').load(
          'ProcesoRemDetalle/MovimientosHistoricos.php?IdRemision=<?php echo $IdRemision; ?>&IdRemisionEncabezado=<?php echo $IdRemisionEncabezado; ?>&IdAlmacen=<?php echo $IdAlmacen; ?>',
          function () {
            $('#movimientosHistoricos').modal('show');
            $(document).off('click.modal-close').on('click.modal-close',
              '[data-dismiss="modal"], .btn-close, .modal-close',
              function () {
                $('#movimientosHistoricos').modal('hide');
              }
            );
          });
      }

      reinicializarEventos();

      $(document).on('hidden.bs.modal', '.modal', function () {
        $(this).remove();
      });
    });
  </script>
</body>

</html>