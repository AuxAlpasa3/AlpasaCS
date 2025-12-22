<?php
Include_once "../templates/head.php";
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php 
     Include_once  "../templates/nav.php";
     Include_once  "../templates/aside.php";

     require_once '../vendor/autoload.php';
     require "../lib/phpqrcode/qrlib.php"; 
     ?>
    <div class="content-wrapper">
      <section class="content mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <br>
              <div class="card">
                <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h3>IMPRIMIR TARJA</h3>
                </div>
                <div class="card-body">
                  <form name="ImprimirTarja" id="ImprimirTarja" action="GenerarTarjaRoyal.php" target="_blank" method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">

                      <!-- SELECT ALMACÉN -->
                      <div class="col-md-6" style="text-align: center;">
                        <label for="Almacen">Seleccionar Almacén</label>
                        <div class="form-group">
                          <?php  
                            $Almacenes = $Conexion->query("SELECT DISTINCT t3.IdAlmacen, t3.NumRecinto,t3.Almacen 
                              FROM t_ingreso t1
                              INNER JOIN t_usuario_almacen t2 ON t1.Almacen=t2.IdAlmacen
                              INNER JOIN t_almacen t3 ON t1.Almacen=t3.IdAlmacen
                              WHERE t2.IdUsuario=$IdUsuario and Estatus=4
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

                      <!-- SELECT TARJA -->
                      <div class="col-md-6" style="text-align: center;">
                        <label for="IdTarja">Seleccione la <b>TARJA</b> a Imprimir</label>
                        <div class="form-group">
                          <select class="form-control select2" name="IdTarja" id="IdTarja" required>
                            <option value="">Seleccione un almacén primero...</option>
                          </select>
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.select2').select2({
        placeholder: "Buscar tarja...",
        allowClear: true,
        width: '100%'
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
        } else {
          $('#IdTarja').html('<option value="">Seleccione un almacén primero...</option>');
        }
      });
    });
  </script>
</body>
</html>
