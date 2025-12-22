<?php
    Include_once "../templates/head.php";
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
                  <h3>IMPRIMIR TARJA</h3>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                          <form name="ImprimirTarja" id="ImprimirTarja" action="GenerarTarjaRoyal.php" target="_blank" method="POST" enctype="multipart/form-data">
                             <div class="col-md-12" style="text-align: center;">
                                <h3 for="IdTarja">Seleccione la <b>TARJA</b> a Imprimir</h3>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT Idtarja FROM t_salida GROUP BY Idtarja HAVING MIN(Estatus) = 4 AND MAX(Estatus) = 4;"); 
                                          $tarja = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                            $hayRegistros = count($tarja) > 0;
                                        ?>
                                        
                                        <?php if ($hayRegistros): ?>
                                            <select class="form-control select2" name="IdTarja" id="IdTarja">
                                                <?php foreach($tarja as $tarjas): ?>
                                                    <option value="<?php echo $tarjas->Idtarja; ?>">  
                                                        <?php echo 'ALPSV-SAL-'.sprintf("%04d", $tarjas->Idtarja); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <select class="form-control select2" name="IdTarja" id="IdTarja" disabled>
                                                <option>No hay registros disponibles</option>
                                            </select>
                                            <div class="alert alert-info mt-2">
                                                No hay registros de tarjas disponibles.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="ImprimirTarja" id="Mov">IMPRIMIR</button>
                            </div>
                        </div>
                    </form>
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
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Buscar tarja...",
            allowClear: true,
            width: '100%'
        });
    });
  </script>
</body>
</html>