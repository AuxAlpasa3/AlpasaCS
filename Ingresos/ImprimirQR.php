<?php
Include_once "../templates/head.php";
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php 
  require_once '../vendor/autoload.php';
  require "../lib/phpqrcode/qrlib.php"; 
  Include_once  "../templates/nav.php";
  Include_once  "../templates/aside.php";
  ?>
  <div class="content-wrapper">
    <section class="content mt-4">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <br>
            <div class="card">
              <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                <h3>IMPRIMIR QR POR ID DE TARJA</h3>
              </div>
              <div class="card-body">
                <form name="Imprimir" id="Imprimir" action="GenerarDocQRMultiple.php" method="POST" enctype="multipart/form-data">
                  <div class="row" style="align-content: center;">

                    <div class="col-md-3" style="text-align: center;">
                      <label for="Almacen">Seleccionar Almacén</label>
                      <div class="form-group">
                        <?php  
                          $Almacenes = $Conexion->query("SELECT DISTINCT t3.IdAlmacen, t3.NumRecinto,t3.Almacen FROM t_ingreso t1
                            INNER JOIN t_usuario_almacen t2 ON t1.Almacen=t2.IdAlmacen
                            INNER JOIN t_almacen t3 ON t1.Almacen=t3.IdAlmacen
                            WHERE t2.IdUsuario=$IdUsuario 
                            ORDER BY t3.NumRecinto")->fetchAll(PDO::FETCH_OBJ);
                        ?>
                        <select class="form-control" name="IdAlmacen" id="IdAlmacen" required>
                          <option value="">Seleccione un almacén...</option>
                          <?php foreach($Almacenes as $alm){ ?>
                            <option value="<?php echo $alm->IdAlmacen; ?>">
                              <?php echo $alm->Almacen; ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <!-- SELECT IDTARJAS -->
                    <div class="col-md-3" style="text-align: center;">
                      <label for="IdTarja">Seleccionar IDTarja</label>
                      <div class="form-group">
                        <select class="form-control select2" name="IdTarja" id="IdTarja"></select>
                      </div>
                    </div>

                    <div class="col-md-3" style="text-align: center;">
                      <label for="Impresora">Seleccionar Impresora</label>
                      <div class="form-group">
                        <select class="form-control" name="NombreImpresora" id="NombreImpresora"></select>
                      </div>
                    </div>

                    <div class="col-md-3" style="text-align: center;">
                      <label for="Cantidad">Seleccionar Cantidad</label>
                      <div class="form-group">
                        <input type="number" class="form-control" id="Cantidad" name="Cantidad" value="1" min="1">
                      </div>
                    </div>

                    <!-- BOTÓN -->
                    <div class="col-md-12" style="text-align: center;">
                      <div class="form-group">
                        <button class="btn btn-success" type="submit" name="Mov" value="ImprimirTarja" id="Mov">IMPRIMIR</button>
                      </div>
                    </div>

                  </div>
                </form>
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
</body>
</html>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
  $('.select2').select2({
    placeholder: "Seleccione uno o más IDTarja...",
    allowClear: true,
    width: '100%',
    closeOnSelect: false 
  });

  $('#IdAlmacen').on('change', function() {
    var almacenId = $(this).val();
    if(almacenId){
      $.ajax({
        url: 'ajax_getIdTarjas.php',
        type: 'POST',
        data: {IdAlmacen: almacenId},
        success: function(data){
          $('#IdTarja').html(data).trigger('change');
        }
      });

      $.ajax({
        url: 'ajax_getImpresoras.php',
        type: 'POST',
        data: {IdAlmacen: almacenId},
        success: function(data){
          $('#NombreImpresora').html(data);
        }
      });
    } else {
      $('#IdTarja').html('');
      $('#NombreImpresora').html('');
    }
  });
});
</script>