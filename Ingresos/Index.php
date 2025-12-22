<?php
    Include_once "../templates/head.php";

    require_once '../vendor/autoload.php';
        require "../lib/phpqrcode/qrlib.php"; 

      use PhpOffice\PhpWord\PhpWord;
      use PhpOffice\PhpWord\IOFactory;
      use PhpOffice\PhpWord\Style\Font;

 function obtenerColorPorEstado($idEstado) {
        $colores = [
            1 => '#28a745', 2 => '#dc3545', 3 => '#fd7e14', 4 => '#ffc107', 5 => '#17a2b8', 6 => '#6f42c1',7 => '#e83e8c', 8 => '#20c997', 9 => '#6610f2', 10 => '#d63384', 11 => '#6c757d', 12 => '#0dcaf0', 13 => '#ff6b6b', 14 => '#4ecdc4', 15 => '#ff9f43', 16 => '#a55eea', 17 => '#45aaf2', 18 => '#fc5c65', 19 => '#fd9644', 20 => '#786fa6', 
        ];
        
        if (isset($colores[$idEstado])) {
            return $colores[$idEstado];
        }
    
        $hash = md5($idEstado);
        return '#' . substr($hash, 0, 6);
    }
?>
<style>
    .highlighted td {
        background: #c3c3c3;
    }
    .badge-estado {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 110%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        margin: 2px;
        color: white;
    }
</style>
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
                  <h1 class="card-title">INGRESOS</h1>
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
                          <div class="col-sm-4">
                            <div class="form-group">
                                <label for="FolioIngreso">Folio Ingreso: </label>
                                <?php  
                                    $sentencia = $Conexion->query("SELECT DISTINCT(t1.IdTarja) AS FolioIngreso,t3.NumRecinto
                                    FROM t_ingreso as t1 
                                    inner join t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen
                                    inner join t_almacen as t3 on t3.IdAlmacen=t1.Almacen
                                    WHERE t1.ESTATUS IN(4,5) and t2.IdUsuario=$IdUsuario Order by IdTarja;"); 
                                    $TDoc = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                ?>
                                <select class="form-control select2" name="FolioIngreso[]" id="FolioIngreso" multiple="multiple" data-placeholder="Seleccione folios">
                                    <?php foreach($TDoc as $doc){ ?>
                                        <option value="<?php echo $doc->FolioIngreso; ?>"> 
                                           <?php echo 'ALP'.$doc->NumRecinto.'-ING-'.sprintf("%04d", $doc->FolioIngreso);?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="col-sm-2">
                              <div class="form-group">
                                  <label for="Articulo">Articulo: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(t1.IdArticulo) as ArticuloNum, t2.MaterialNo as MaterialNo 
                                        FROM t_ingreso as t1 
                                        inner join t_articulo as t2 on t1.IdArticulo=t2.IdArticulo  
                                        inner join t_usuario_almacen as t3 on t1.Almacen=t3.IdAlmacen
                                        WHERE t1.ESTATUS IN(4,5) and t3.IdUsuario=$IdUsuario Order by t1.IdArticulo"); 
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
                          <div class="col-sm-2">
                              <div class="form-group">
                                  <label for="Ubicacion">Ubicacion: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(t1.IdUbicacion) as UbicacionNum,t2.Ubicacion as Ubicacion 
                                        FROM t_ingreso as t1 
                                        inner join t_ubicacion as t2 on t1.IdUbicacion=t2.IdUbicacion  
                                        inner join t_usuario_almacen as t3 on t1.Almacen=t3.IdAlmacen
                                        WHERE t1.ESTATUS IN(4,5) and t3.IdUsuario=$IdUsuario order by t1.IdUbicacion"); 
                                      $Ubicaciones = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                  ?>
                                  <select class="form-control select2" name="Ubicacion[]" id="Ubicacion" multiple="multiple" data-placeholder="Seleccione ubicaciones">
                                      <?php foreach($Ubicaciones as $Ubicacion){ ?>
                                          <option value="<?php echo $Ubicacion->UbicacionNum; ?>"> 
                                              <?php echo $Ubicacion->Ubicacion; ?>
                                          </option>
                                      <?php } ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-sm-2">
                              <div class="form-group">
                                  <label for="Cliente">Cliente: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(t1.Cliente) as ClienteNum,t2.NombreCliente as Cliente  
                                        FROM t_ingreso as t1 
                                        inner join t_cliente as t2 on t1.Cliente=t2.IdCliente  
                                        inner join t_usuario_almacen as t3 on t1.Almacen=t3.IdAlmacen
                                        WHERE t1.ESTATUS IN(4,5) and t3.IdUsuario=$IdUsuario"); 
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
                          <div class="col-sm-2">
                              <div class="form-group">
                                  <label for="NoTarima">No Tarima: </label>
                                  <?php  
                                      $sentencia = $Conexion->query("SELECT distinct(NoTarima) as NoTarima  
                                      FROM t_ingreso as t1
                                      INNER JOIN t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen
                                      WHERE t1.ESTATUS IN(4,5) and t2.IdUsuario=$IdUsuario"); 
                                      $Tarimas = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                  ?>
                                  <select class="form-control select2" name="NoTarima[]" id="NoTarima" multiple="multiple" data-placeholder="Selecciona el NoTarima">
                                      <?php foreach($Tarimas as $NoTarimas){ ?>
                                          <option value="<?php echo $NoTarimas->NoTarima; ?>"> 
                                              <?php echo $NoTarimas->NoTarima; ?>
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
              $FolioIngreso = isset($_POST['FolioIngreso']) && is_array($_POST['FolioIngreso']) ? $_POST['FolioIngreso'] : [];
              $Cliente = isset($_POST['Cliente']) && is_array($_POST['Cliente']) ? $_POST['Cliente'] : [];
              $Ubicacion = isset($_POST['Ubicacion']) && is_array($_POST['Ubicacion']) ? $_POST['Ubicacion'] : [];
              $Articulo = isset($_POST['Articulo']) && is_array($_POST['Articulo']) ? $_POST['Articulo'] : [];   
              $NoTarima = isset($_POST['NoTarima']) && is_array($_POST['NoTarima']) ? $_POST['NoTarima'] : [];

                    $sql="SELECT t1.IdTarja AS IdTarja,  t1.IdTarja as IdTarjaNum,  t1.CodBarras AS CodBarras,  t1.CodBarras as CodBarrasNum,   CONVERT(DATE, t1.FechaIngreso) as FechaIngreso,  t1.FechaProduccion,  t1.IdArticulo,  t2.MaterialNo,   TRIM(CONCAT(t2.Material, ' ', t2.Shape)) as MaterialShape,  t1.Piezas,  t1.NumPedido,  t1.NetWeight,  t1.GrossWeight,  t1.IdUbicacion,  t3.Ubicacion, STUFF(( SELECT ', ' + tem.EstadoMaterial FROM STRING_SPLIT(t1.EstadoMercancia, ',') estado INNER JOIN t_estadoMaterial tem ON CAST(estado.value AS INT) = tem.IdEstadoMaterial FOR XML PATH('') ), 1, 2, '') as EstadoMercancia,t1.EstadoMercancia as EstadosIds,t1.Origen, t1.Cliente, t4.NombreCliente, t7.IdRemision, t1.IdLinea, t1.Transportista, TRIM(t1.Placas) as Placas, t1.Chofer, t1.Checador, t1.Supervisor,  (CASE WHEN t1.Comentarios IS NULL THEN 'SIN COMENTARIOS' ELSE t1.Comentarios END) as Comentarios,  (CASE WHEN t1.PaisOrigen IS NULL THEN 'Sin Pais Origen Registrado' ELSE t1.PaisOrigen END) as PaisOrigen, t1.NoTarima, t8.NumRecinto, t1.Almacen FROM t_ingreso as t1  
                                INNER JOIN t_articulo as t2 ON t1.IdArticulo = t2.IdArticulo 
                                LEFT JOIN t_ubicacion as t3 ON t1.IdUbicacion = t3.IdUbicacion 
                                INNER JOIN t_cliente as t4 ON t1.Cliente = t4.IdCliente  
                                INNER JOIN t_usuario_almacen as t6 ON t1.Almacen = t6.IdAlmacen  
                                INNER JOIN t_remision_encabezado as t7 ON t1.IdRemision = t7.IdRemisionEncabezado 
                                INNER JOIN t_almacen as t8 ON t1.Almacen = t8.IdAlmacen 
                                WHERE t1.ESTATUS IN (4,5) AND t6.IdUsuario = $IdUsuario ";
                      if(!empty($FolioIngreso)) 
                      {
                        $folioList = implode(",", array_map('intval', $FolioIngreso)); 
                        $sql .= " AND t1.IdTarja IN ($folioList)";
                      }

                      if(!empty($Cliente)) {
                        $clientesEscapados = array_map(function($value) use ($Conexion) {
                            return $Conexion->quote($value); }, $Cliente);
                        $clienteList = implode(",", $clientesEscapados);
                            $sql .= " AND t4.IdCliente IN ($clienteList)";
                      }

                      if(!empty($Ubicacion)) {
                        $ubicacionList = implode(",", array_map('intval', $Ubicacion)); 
                        $sql .= " AND t3.IdUbicacion IN ($ubicacionList)";
                      }

                      if(!empty($Articulo)) {
                        $articuloList = implode(",", array_map('intval', $Articulo)); 
                        $sql .= " AND t2.IdArticulo IN ($articuloList)";
                      }

                      if(!empty($NoTarima)) {
                          $NoTarimaList = implode(",", $NoTarima); 
                          $sql .= " AND T1.NoTarima IN ($NoTarimaList)";
                      }

                        $sql .= " ORDER BY t1.IdTarja;";


                      $sentIngresos = $Conexion->query($sql);
                      $Ingresos = $sentIngresos->fetchAll(PDO::FETCH_OBJ);

                    if(count($Ingresos) > 0) {
                  ?>
                          <div class="row">
                            <div class="col-12"> 
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="tablaconB" > 
                                        <thead>
                                          <tr>
                                           <th width="auto" style="color:black; text-align: center;">   <input type="checkbox"  onClick="toggle(this)"></th>
                                           <th width="auto" style="color:black; text-align: center;">Tarja</th>
                                           <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                           <th width="auto" style="color:black; text-align: center;">Fecha Ingreso</th>
                                           <th width="auto" style="color:black; text-align: center;">Fecha Produccion</th>
                                           <th width="auto" style="color:black; text-align: center;">Material No.</th>
                                           <th width="auto" style="color:black; text-align: center;">Material/Shape</th>
                                           <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                           <th width="auto" style="color:black; text-align: center;">Pedido</th>
                                           <th width="auto" style="color:black; text-align: center;">Net Weight</th>
                                           <th width="auto" style="color:black; text-align: center;">Gross Weight</th>
                                           <th width="auto" style="color:black; text-align: center;">Ubicacion</th>
                                           <th width="auto" style="color:black; text-align: center;">Estado Material</th>
                                           <th width="auto" style="color:black; text-align: center;">Pais Origen</th>
                                           <th width="auto" style="color:black; text-align: center;">Origen</th>
                                           <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                           <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                           <th width="auto" style="color:black; text-align: center;">Comentarios</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                             foreach($Ingresos as $Ingreso){
                                              $CodBarras=$Ingreso->CodBarras;
                                              $estadosBadges = '';
                                              if (!empty($Ingreso->EstadosIds)) {
                                                  $idsEstados = explode(',', $Ingreso->EstadosIds);
                                                  $nombresEstados = explode(', ', $Ingreso->EstadoMercancia);
                                                  
                                                  for ($i = 0; $i < count($idsEstados); $i++) {
                                                      $idEstado = trim($idsEstados[$i]);
                                                      $nombreEstado = trim($nombresEstados[$i]);
                                                      
                                                      if (!empty($idEstado) && !empty($nombreEstado)) {
                                                          $color = obtenerColorPorEstado($idEstado);
                                                          $estadosBadges .= '<span class="badge-estado" style="background-color: ' . $color . ';">' . 
                                                                           htmlspecialchars($nombreEstado) . '</span> ';
                                                      }
                                                  }
                                              }
                                              ?>
                                          <tr>
                                            <td width="auto" style="text-align: center;">
                                                <input type="checkbox" name="marcar[]"  value="<?php echo $Ingreso->CodBarrasNum; ?>">
                                            </td>
                                      </form>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo 'ALP'.$Ingreso->NumRecinto.'-ING-'.sprintf("%04d", $Ingreso->IdTarja);?>
                                            </td> 
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NumRecinto."-". sprintf("%06d", $CodBarras=$Ingreso->CodBarras);?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->FechaIngreso;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->FechaProduccion;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->MaterialNo;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->MaterialShape;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Piezas;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NumPedido;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo number_format($Ingreso->NetWeight, 0, '.', ',');?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo number_format($Ingreso->GrossWeight, 0, '.', ',');?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Ubicacion;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $estadosBadges; ?>
                                            </td>
                                             <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->PaisOrigen;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Origen;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NombreCliente;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                            <?php echo $Ingreso->IdRemision;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                            <?php echo $Ingreso->Comentarios;?>
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
    
    $('#ingresoTable').DataTable({
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