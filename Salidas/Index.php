<?php
    Include_once "../templates/head.php";
      
        require_once '../vendor/autoload.php';
        require "../lib/phpqrcode/qrlib.php"; 
?>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php 
     Include_once  "../templates/nav.php";
     Include_once  "../templates/aside.php";
     ?>
    <div class="content-wrapper">
      <section class="content mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <BR>
              <div class="card">
                <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h1 class="card-title">SALIDAS</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                    <form name="buscar" id="buscar" method="POST" enctype="multipart/form-data">
                      <br>
                      <div class="form-group">
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                                <label for="FolioSalida">Folio Salida: </label>
                                <?php  
                                    $sentencia = $Conexion->query("SELECT DISTINCT(IdTarja) AS FolioSalida FROM t_salida WHERE ESTATUS IN(4,5) order by IdTarja ;"); 
                                    $TDoc = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                ?>
                                <select class="form-control select2" name="FolioSalida[]" id="FolioSalida" multiple="multiple" data-placeholder="Seleccione folios">
                                    <?php foreach($TDoc as $doc){ ?>
                                        <option value="<?php echo $doc->FolioSalida; ?>"> 
                                            <?php echo 'ALPSV-SAL-'.sprintf("%04d", $doc->FolioSalida);?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="Articulo">Articulo: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(t1.IdArticulo) as ArticuloNum, t2.MaterialNo as MaterialNo FROM t_salida as t1 inner join t_articulo as t2 on t1.IdArticulo=t2.IdArticulo  WHERE t1.ESTATUS IN(4,5) order by t1.IdArticulo"); 
                                      $Articulos = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                  ?>
                                  <select class="form-control select2" name="Articulo[]" id="Articulo" multiple="multiple" data-placeholder="Seleccione artículos">
                                      <?php foreach($Articulos as $Articulo){ ?>
                                          <option value="<?php echo $Articulo->ArticuloNum; ?>"> 
                                              <?php echo $Articulo->MaterialNo; ?>
                                          </option>
                                      <?php } ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="Cliente">Cliente: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(t1.Cliente) as ClienteNum,t2.NombreCliente as Cliente  FROM t_salida as t1 inner join t_cliente as t2 on t1.Cliente=t2.IdCliente  WHERE ESTATUS IN(4,5)"); 
                                      $Clientes = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                  ?>
                                  <select class="form-control select2" name="Cliente[]" id="Cliente" multiple="multiple" data-placeholder="Seleccione clientes">
                                      <?php foreach($Clientes as $Cliente){ ?>
                                          <option value="<?php echo $Cliente->ClienteNum; ?>"> 
                                              <?php echo $Cliente->Cliente; ?>
                                          </option>
                                      <?php } ?>
                                  </select>
                              </div>
                          </div>
                        </div>
                      </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" id="aplicar-filtros" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                  <?php 

                    $FolioSalida = isset($_POST['FolioSalida']) && is_array($_POST['FolioSalida']) ? $_POST['FolioSalida'] : [];
                    $Cliente = isset($_POST['Cliente']) && is_array($_POST['Cliente']) ? $_POST['Cliente'] : [];
                    $Articulo = isset($_POST['Articulo']) && is_array($_POST['Articulo']) ? $_POST['Articulo'] : [];   

                    $sql="SELECT t1.IdTarja AS IdTarja, t1.CodBarras AS CodBarras, CONVERT(DATE,t1.FechaSalida) as FechaSalida, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo, trim(Concat(t2.Material,t2.Shape)) as MaterialShape, t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.Cliente,t4.NombreCliente,t1.IdRemision,t1.IdLinea
                          FROM t_salida as t1 
                          INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                          INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente 
                        WHERE  ESTATUS IN (3,4)";

                      if(!empty($FolioSalida)) 
                      {
                        $folioList = implode(",", array_map('intval', $FolioSalida)); 
                        $sql .= " AND t1.IdTarja IN ($folioList)";
                      }

                      if(!empty($Cliente)) {
                        $clientesEscapados = array_map(function($value) use ($Conexion) {
                            return $Conexion->quote($value); }, $Cliente);
                        $clienteList = implode(",", $clientesEscapados);
                            $sql .= " AND t4.IdCliente IN ($clienteList)";
                      }

                      if(!empty($Articulo)) {
                        $articuloList = implode(",", array_map('intval', $Articulo)); 
                        $sql .= " AND t2.IdArticulo IN ($articuloList)";
                      }

                        $sql .= " ORDER BY t1.IdTarja;";

                          $sentSalidas = $Conexion->query($sql);
                      $Salidas = $sentSalidas->fetchAll(PDO::FETCH_OBJ);

                    if(count($Salidas) > 0) {
                  ?>
                  <div class="row">
                    <div class="col-12">
                          <section class="pt-2">
                            <div class="table-responsive">
                              <table class="table table-bordered  table-striped" id="salidaTable" > 
                                <thead>
                                  <tr>
                                   <th width="auto" style="color:black; text-align: center;">Tarja</th>
                                   <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                   <th width="auto" style="color:black; text-align: center;">Fecha Salida</th>
                                   <th width="auto" style="color:black; text-align: center;">Fecha Produccion</th>
                                   <th width="auto" style="color:black; text-align: center;">Material No.</th>
                                   <th width="auto" style="color:black; text-align: center;">Material/Shape</th>
                                   <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                   <th width="auto" style="color:black; text-align: center;">Pedido</th>
                                   <th width="auto" style="color:black; text-align: center;">Net Weight</th>
                                   <th width="auto" style="color:black; text-align: center;">Gross Weight</th>
                                   <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                   <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($Salidas as $Salida){
                                      $CodBarras=$Salida->CodBarras
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                       <?php echo 'ALPSV-SAL-'.sprintf("%04d", $Salida->IdTarja);?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo 'ALP-'.sprintf("%06d", $CodBarras=$Salida->CodBarras);?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->FechaSalida;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->FechaProduccion;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->MaterialNo;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->MaterialShape;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->Piezas;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->NumPedido;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->NetWeight;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->GrossWeight;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->NombreCliente;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->IdRemision;?>
                                    </td>
                                   </tr>
                                          <?php  
                                           
                                              }
                                          ?>
                                       </tbody>
                                      </table>
                                    </div>
                                  </section>
                                </div>
                              </div>
                              <?php
                                } else {
                                  echo '<div class="alert alert-info">No se encontraron registros con los filtros seleccionados</div>';
                                }
                              ?>
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
  </div>
  
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
  
  <script>

    $(document).ready(function() {
    $('.select2').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true,
        closeOnSelect: false,  
        width: '100%'
    });
    
    $('.select2').on('select2:select select2:unselect', function(e) {
    });
    
    $('#aplicar-filtros').on('click', function() {
        $('#buscar').submit();
    });
    
    $('#salidaTable').DataTable({
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
                extend: 'pdfHtml5',
                text: 'Exportar a PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 10;
                    doc.pageMargins = [20, 20, 20, 20];
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
    
});
  </script>
</body>