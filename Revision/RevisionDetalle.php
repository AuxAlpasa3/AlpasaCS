<?php
include_once "../templates/head.php";

$IdRevision = $_GET['id'];
$ubicacion = $_GET['ubicacion'];
$IdUbicacion = $_GET['UbicacionNom'];
?>



<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php
    include_once "../templates/nav.php";
    include_once "../templates/aside.php";
    ?>
    <div class="content-wrapper">
      <section class="content mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header text-white"
                  style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h1 class="card-title">REVISION DETALLE: <?php echo $IdRevision; ?> UBICACION:
                    <?php echo $ubicacion; ?>
                  </h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                          <form action="DocumentoResumen.php" method="post" target="_blank">
                            <input type="hidden" name="IdUbicacion" value="<?php echo $IdUbicacion; ?>">
                            <input type="hidden" name="IdRevision" value="<?php echo $IdRevision; ?>">
                          </form>

                          <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                              <a class="nav-link active" id="coincidentes-tab" data-toggle="tab" href="#coincidentes"
                                role="tab" aria-controls="coincidentes" aria-selected="true">Coincidentes</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="no-coincidentes-tab" data-toggle="tab" href="#no-coincidentes"
                                role="tab" aria-controls="no-coincidentes" aria-selected="false">No Coincidentes</a>
                            </li>
                          </ul>

                          </td>
                          <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="coincidentes" role="tabpanel"
                              aria-labelledby="coincidentes-tab">
                              <?php
                              $sentCoincidentes = $Conexion->query("SELECT 
                                  t4.Almacen, 
                                  T1.FolioIngreso AS FolioIngreso, 
                                  (CASE WHEN t1.FechaIngreso IS NULL 
                                      THEN 'SIN FECHA' 
                                      ELSE CONVERT(VARCHAR(10), CONVERT(DATE, T1.FechaIngreso), 120) 
                                  END) as FechaIngreso,
                                  T1.FechaProduccion, 
                                  TRIM(CONCAT(t2.Material, t2.Shape)) as MaterialShape, 
                                  T1.MaterialNo, 
                                  T1.Piezas, 
                                  T1.NumPedido, 
                                  T1.CodBarras, 
                                  T1.NetWeight, 
                                  T1.GrossWeight, 
                                  (CASE WHEN T1.IdUbicacion = 0 THEN 'SIN UBICACIÓN' ELSE t3.Ubicacion END) AS Ubicacion, 
                                  T6.EstadoMaterial, 
                                  T1.Origen, 
                                  T5.NombreCliente as Cliente,
                                  (CASE WHEN T1.dias = 0 THEN 'SIN PERIODICIDAD' 
                                        WHEN T1.Almacenaje < T1.dias THEN 'VIGENTE' 
                                        WHEN T1.Almacenaje > T1.dias THEN 'VENCIDO' 
                                  END) AS Periodicidad,
                                  T1.PaisOrigen, 
                                  T1.NoTarima, 
                                  T1.Almacenaje,
                                  t1.IdRemision, t7.Comentarios
                              FROM t_lecturaQR as t7
                              INNER JOIN t_inventario AS T1 ON t1.IdUbicacion = t7.IdUbicacion and t7.CodBarras=t1.CodBarras
                              INNER JOIN t_articulo as T2 ON T1.IdArticulo = T2.IdArticulo 
                              LEFT JOIN t_ubicacion as T3 ON T1.IdUbicacion = T3.IdUbicacion 
                              LEFT JOIN t_almacen as T4 ON T1.Almacen = T4.IdAlmacen
                              LEFT JOIN t_cliente as t5 ON T1.Cliente = T5.IdCliente
                              LEFT JOIN t_estadoMaterial as T6 ON T1.EstadoMaterial = T6.IdEstadoMaterial
                              WHERE t7.IdRevision = $IdRevision 
                                AND t7.IdUbicacion = $IdUbicacion 
                                AND t7.Estado = 1
                                AND t1.EnProceso = 0;");
                              $Coincidentes = $sentCoincidentes->fetchAll(PDO::FETCH_OBJ);
                              ?>
                              <div class="table-responsive pt-3">
                                <table class="table table-bordered table-striped" id="tablarevision">
                                  <thead>
                                    <tr>
                                      <th width="auto" style="color:black; text-align: center;">Almacen</th>
                                      <th width="auto" style="color:black; text-align: center;">Folio Ingreso</th>
                                      <th width="auto" style="color:black; text-align: center;">Fecha Ingreso</th>
                                      <th width="auto" style="color:black; text-align: center;">Fecha Produccion</th>
                                      <th width="auto" style="color:black; text-align: center;">Material No.</th>
                                      <th width="auto" style="color:black; text-align: center;">Material/Shape</th>
                                      <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                      <th width="auto" style="color:black; text-align: center;">Pedido</th>
                                      <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                      <th width="auto" style="color:black; text-align: center;">Net Weight</th>
                                      <th width="auto" style="color:black; text-align: center;">Gross Weight</th>
                                      <th width="auto" style="color:black; text-align: center;">Estado Material</th>
                                      <th width="auto" style="color:black; text-align: center;">Pais Origen</th>
                                      <th width="auto" style="color:black; text-align: center;">Origen</th>
                                      <th width="auto" style="color:black; text-align: center;">No. Tarima</th>
                                      <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                      <th width="auto" style="color:black; text-align: center;">Periodicidad</th>
                                      <th width="auto" style="color:black; text-align: center;">Almacenaje</th>
                                      <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                      <th width="auto" style="color:black; text-align: center;">Comentarios</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach ($Coincidentes as $item): ?>
                                      <tr>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Almacen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo 'ALPSV-ING-' . sprintf("%04d", $item->FolioIngreso); ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->FechaIngreso; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->FechaProduccion; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->MaterialNo; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->MaterialShape; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Piezas; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NumPedido; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo 'ALP-' . sprintf("%06d", $item->CodBarras); ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NetWeight; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->GrossWeight; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->EstadoMaterial; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->PaisOrigen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Origen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NoTarima; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Cliente; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Periodicidad; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Almacenaje; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->IdRemision; ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo $item->Comentarios; ?></td>
                                      </tr>
                                    <?php endforeach; ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>

                            <!-- Pestaña No Coincidentes -->
                            <div class="tab-pane fade" id="no-coincidentes" role="tabpanel"
                              aria-labelledby="no-coincidentes-tab">
                              <?php
                              $sentNoCoincidentes = $Conexion->query("SELECT 
                                          t4.Almacen, 
                                          T1.FolioIngreso AS FolioIngreso, 
                                          (CASE WHEN t1.FechaIngreso IS NULL 
                                              THEN 'SIN FECHA' 
                                              ELSE CONVERT(VARCHAR(10), CONVERT(DATE, T1.FechaIngreso), 120) 
                                          END) as FechaIngreso,
                                          T1.FechaProduccion, 
                                          TRIM(CONCAT(t2.Material, t2.Shape)) as MaterialShape, 
                                          T1.MaterialNo, 
                                          T1.Piezas, 
                                          T1.NumPedido, 
                                          T1.CodBarras, 
                                          T1.NetWeight, 
                                          T1.GrossWeight, 
                                          (CASE WHEN T1.IdUbicacion = 0 THEN 'SIN UBICACIÓN' ELSE t3.Ubicacion END) AS Ubicacion, 
                                          T6.EstadoMaterial, 
                                          T1.Origen, 
                                          T5.NombreCliente as Cliente,
                                          (CASE WHEN T1.dias = 0 THEN 'SIN PERIODICIDAD' 
                                                WHEN T1.Almacenaje < T1.dias THEN 'VIGENTE' 
                                                WHEN T1.Almacenaje > T1.dias THEN 'VENCIDO' 
                                          END) AS Periodicidad,
                                          T1.PaisOrigen, 
                                          T1.NoTarima, 
                                          T1.Almacenaje,
                                          t1.IdRemision, t7.Estado, t7.Comentarios
                                      FROM t_lecturaQR as t7
                                      INNER JOIN t_inventario AS T1 ON t1.IdUbicacion = t7.IdUbicacion and t7.CodBarras=t1.CodBarras
                                      INNER JOIN t_articulo as T2 ON T1.IdArticulo = T2.IdArticulo 
                                      LEFT JOIN t_ubicacion as T3 ON T1.IdUbicacion = T3.IdUbicacion 
                                      LEFT JOIN t_almacen as T4 ON T1.Almacen = T4.IdAlmacen
                                      LEFT JOIN t_cliente as t5 ON T1.Cliente = T5.IdCliente
                                      LEFT JOIN t_estadoMaterial as T6 ON T1.EstadoMaterial = T6.IdEstadoMaterial
                                      WHERE t7.IdRevision = $IdRevision 
                                        AND t7.IdUbicacion = $IdUbicacion 
                                        AND t7.Estado> 1
                                        AND t1.EnProceso = 0;");
                              $NoCoincidentes = $sentNoCoincidentes->fetchAll(PDO::FETCH_OBJ);
                              ?>
                              <div class="table-responsive pt-3">
                                <table class="table table-bordered table-striped" id="tablaNoCoincidentes">
                                  <thead>
                                    <tr>
                                      <th width="auto" style="color:black; text-align: center;">Almacen</th>
                                      <th width="auto" style="color:black; text-align: center;">Folio Ingreso</th>
                                      <th width="auto" style="color:black; text-align: center;">Fecha Ingreso</th>
                                      <th width="auto" style="color:black; text-align: center;">Fecha Produccion</th>
                                      <th width="auto" style="color:black; text-align: center;">Material No.</th>
                                      <th width="auto" style="color:black; text-align: center;">Material/Shape</th>
                                      <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                      <th width="auto" style="color:black; text-align: center;">Pedido</th>
                                      <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                      <th width="auto" style="color:black; text-align: center;">Net Weight</th>
                                      <th width="auto" style="color:black; text-align: center;">Gross Weight</th>
                                      <th width="auto" style="color:black; text-align: center;">Estado Material</th>
                                      <th width="auto" style="color:black; text-align: center;">Pais Origen</th>
                                      <th width="auto" style="color:black; text-align: center;">Origen</th>
                                      <th width="auto" style="color:black; text-align: center;">No. Tarima</th>
                                      <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                      <th width="auto" style="color:black; text-align: center;">Periodicidad</th>
                                      <th width="auto" style="color:black; text-align: center;">Almacenaje</th>
                                      <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                      <th width="auto" style="color:black; text-align: center;">Comentarios</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach ($NoCoincidentes as $item): ?>
                                      <tr>

                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Almacen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo 'ALPSV-ING-' . sprintf("%04d", $item->FolioIngreso); ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->FechaIngreso; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->FechaProduccion; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->MaterialNo; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->MaterialShape; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Piezas; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NumPedido; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo 'ALP-' . sprintf("%06d", $item->CodBarras); ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NetWeight; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->GrossWeight; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->EstadoMaterial; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->PaisOrigen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Origen; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->NoTarima; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Cliente; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Periodicidad; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->Almacenaje; ?>
                                        </td>
                                        <td width="auto" style="text-align: center;">
                                          <?php echo $item->IdRemision; ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo $item->Comentarios; ?></td>
                                      </tr>
                                    <?php endforeach; ?>
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
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include_once '../templates/footer.php' ?>
    <aside class="control-sidebar">
    </aside>
    <script>
      $('#tablarevision').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": false,
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "dom": 'Bfrtip',
        "buttons": [
          {
            extend: 'excelHtml5',
            text: 'Exportar a Excel',
            className: 'btn btn-success',
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            text: 'Descargar',
            className: 'btn btn-warning',
            action: function (e, dt, node, config) {
              document.querySelector('form[action="DocumentoResumen.php"]').submit();
            }
          },
          {
            extend: 'print',
            text: 'Imprimir',
            className: 'btn btn-info',
            exportOptions: {
              columns: ':visible'
            }
          }
        ]
      });

      $('#tablaNoCoincidentes').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": false,
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "dom": 'Bfrtip',
        "buttons": [
          {
            extend: 'excelHtml5',
            text: 'Exportar a Excel',
            className: 'btn btn-success',
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            text: 'Descargar',
            className: 'btn btn-warning',
            action: function (e, dt, node, config) {
              document.querySelector('form[action="DocumentoResumen.php"]').submit();
            }
          },
          {
            extend: 'print',
            text: 'Imprimir',
            className: 'btn btn-info',
            exportOptions: {
              columns: ':visible'
            }
          }
        ]
      });
    </script>