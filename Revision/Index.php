<?php
include_once "../templates/head.php";
?>
<style>
  .highlighted td {
    background: #ffa500;
  }

  .nav-tabs .nav-link.active {
    background-color: #d94f00;
    color: white;
  }

  .nav-tabs .nav-link {
    color: #d94f00;
  }

  .btn-agregar {
    background-color: #d94f00 !important;
    border-color: #d94f00 !important;
  }

  .btn-detalle {
    background-color: #d94f00 !important;
  }

  .btn-cerrar {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
  }
</style>

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
              <BR>
              <div class="card">
                <div class="card-header text-white"
                  style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h1 class="card-title">REVISIONES</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                          <?php
                          $sentRevision = $Conexion->query("SELECT t1.IdRevision,t2.TipoRevision,
                          t1.Descripcion,FORMAT(t1.FechaInicio,'dd/MM/yyyy') AS FechaInicio ,
                          FORMAT(t1.FechaFinal,'dd/MM/yyyy') AS FechaFinal, t1.Estatus, (Case when t1.Estatus=0 then 'Creada' When t1.Estatus=1 then 'En Revisión' When t1.Estatus=2 then 'Cerrada' End) as EstatusTexto
                          FROM dbo.t_Revision AS t1
                          INNER JOIN dbo.t_tipoRevision AS t2 ON t2.IdTipoRevision = t1.TipoRevision");
                          $Revisiones = $sentRevision->fetchAll(PDO::FETCH_OBJ);
                          ?>
                          <div class="row">
                            <div class="col-12">
                              <button type="button" class="btn-agregar btn btn-primary btn-g">
                                <i class="fa fa-plus"></i> Añadir Nuevo Periodo
                              </button>

                              <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link active" id="creada-tab" data-bs-toggle="tab"
                                    data-bs-target="#creada" type="button" role="tab" aria-controls="creada"
                                    aria-selected="true">Creada</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link" id="revision-tab" data-bs-toggle="tab"
                                    data-bs-target="#revision" type="button" role="tab" aria-controls="revision"
                                    aria-selected="false">En Revisión</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link" id="cerrada-tab" data-bs-toggle="tab"
                                    data-bs-target="#cerrada" type="button" role="tab" aria-controls="cerrada"
                                    aria-selected="false">Cerrada</button>
                                </li>
                              </ul>

                              <div class="tab-content" id="myTabContent">
                                <!-- Pestaña Creada -->
                                <div class="tab-pane fade show active" id="creada" role="tabpanel"
                                  aria-labelledby="creada-tab">
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="dataTableCreada">
                                        <thead>
                                          <tr>
                                            <th width="auto" style="text-align: center;">IdRevision</th>
                                            <th width="auto" style="text-align: center;">Ubicaciones</th>
                                            <th width="auto" style="text-align: center;">TipoRevision</th>
                                            <th width="auto" style="text-align: center;">Descripción</th>
                                            <th width="auto" style="text-align: center;">Fecha Inicio</th>
                                            <th width="auto" style="text-align: center;">Fecha Final</th>
                                            <th width="auto" style="text-align: center;">Estatus</th>
                                            <th width="auto" style="text-align: center;">Acciones</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                          foreach ($Revisiones as $Revision) {
                                            if ($Revision->Estatus == 0) {
                                              $IdRevision = $Revision->IdRevision;
                                              ?>
                                              <tr>
                                                <td style="text-align: center;">
                                                  <?php echo $IdRevision = $Revision->IdRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php
                                                  $sentArmado2 = $Conexion->query("SELECT t2.Ubicacion,t1.IdUbicacion FROM dbo.t_revisionUbicaciones AS t1
                                              INNER JOIN dbo.t_ubicacion AS t2 ON t2.IdUbicacion = t1.IdUbicacion
                                              WHERE t1.IdRevision=$IdRevision ORDER BY t1.IdUbicacion");
                                                  $Armados2 = $sentArmado2->fetchAll(PDO::FETCH_OBJ);
                                                  ?>
                                                  <table class="table table-bordered table-striped"
                                                    style="background-color:white;">
                                                    <thead>
                                                      <tr>
                                                        <th style="text-align: center;">Ubicacion</th>
                                                        <th style="text-align: center;">Acción</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <?php
                                                      foreach ($Armados2 as $Arm2) {
                                                        ?>
                                                        <tr>
                                                          <td style="text-align: center;">
                                                            <?php echo $Arm2->Ubicacion; ?>
                                                          </td>
                                                          <td style="text-align: center;">
                                                            <form action="RevisionDetalle.php" method="GET">
                                                              <input type="hidden" id="id" name="id"
                                                                value="<?php echo $IdRevision; ?>">
                                                              <input type="hidden" id="ubicacion" name="ubicacion"
                                                                value="<?php echo $Arm2->Ubicacion; ?>">
                                                              <input type="hidden" id="UbicacionNom" name="UbicacionNom"
                                                                value="<?php echo $Arm2->IdUbicacion; ?>">
                                                              <button class="btn btn-sm btn-detalle text-white" type="submit">
                                                                <i class="fa fa-eye"></i> Detalle
                                                              </button>
                                                            </form>
                                                          </td>
                                                        </tr>
                                                        <?php
                                                      }
                                                      ?>
                                                    </tbody>
                                                  </table>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->TipoRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->Descripcion; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaInicio; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaFinal; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->EstatusTexto; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <button type="button" class="btn-editar btn btn-warning btn-sm"
                                                    data-id="<?php echo $IdRevision = $Revision->IdRevision; ?>">
                                                    <i class="fa fa-pen"></i> Editar
                                                  </button>
                                                  <button type="button" class="btn-eliminar btn btn-danger btn-sm"
                                                    data-id="<?php echo $IdRevision = $Revision->IdRevision; ?>">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                  </button>
                                                </td>
                                              </tr>
                                              <?php
                                            }
                                          }
                                          ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </section>
                                </div>

                                <!-- Pestaña En Revisión -->
                                <div class="tab-pane fade" id="revision" role="tabpanel" aria-labelledby="revision-tab">
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="dataTableRevision">
                                        <thead>
                                          <tr>
                                            <th width="auto" style="text-align: center;">IdRevision</th>
                                            <th width="auto" style="text-align: center;">Ubicaciones</th>
                                            <th width="auto" style="text-align: center;">TipoRevision</th>
                                            <th width="auto" style="text-align: center;">Descripción</th>
                                            <th width="auto" style="text-align: center;">Fecha Inicio</th>
                                            <th width="auto" style="text-align: center;">Fecha Final</th>
                                            <th width="auto" style="text-align: center;">Estatus</th>
                                            <th width="auto" style="text-align: center;">Acciones</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                          foreach ($Revisiones as $Revision) {
                                            if ($Revision->Estatus == 1) {
                                              $IdRevision = $Revision->IdRevision;

                                              // Consulta para verificar el estatus de las ubicaciones
                                              $sentEstatusUbicaciones = $Conexion->query("SELECT Estatus FROM dbo.t_revisionUbicaciones WHERE IdRevision = $IdRevision");
                                              $estatusUbicaciones = $sentEstatusUbicaciones->fetchAll(PDO::FETCH_OBJ);

                                              // Verificar si todas las ubicaciones tienen estatus = 1
                                              $todasUbicacionesCompletadas = true;
                                              foreach ($estatusUbicaciones as $estatus) {
                                                if ($estatus->Estatus != 1) {
                                                  $todasUbicacionesCompletadas = false;
                                                  break;
                                                }
                                              }
                                              ?>
                                              <tr>
                                                <td style="text-align: center;">
                                                  <?php echo $IdRevision = $Revision->IdRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php
                                                  $sentArmado2 = $Conexion->query("SELECT t2.Ubicacion, t1.IdUbicacion, t1.Estatus 
                                                  FROM dbo.t_revisionUbicaciones AS t1
                                                  INNER JOIN dbo.t_ubicacion AS t2 ON t2.IdUbicacion = t1.IdUbicacion
                                                  WHERE t1.IdRevision=$IdRevision ORDER BY t1.IdUbicacion");
                                                  $Armados2 = $sentArmado2->fetchAll(PDO::FETCH_OBJ);
                                                  ?>
                                                  <table class="table table-bordered table-striped"
                                                    style="background-color:white;">
                                                    <thead>
                                                      <tr>
                                                        <th style="text-align: center;">Ubicacion</th>
                                                        <th style="text-align: center;">Estatus</th>
                                                        <th style="text-align: center;">Acción</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <?php
                                                      foreach ($Armados2 as $Arm2) {
                                                        $estatusTexto = ($Arm2->Estatus == 1) ? 'Completada' : 'Pendiente';
                                                        $estatusClass = ($Arm2->Estatus == 1) ? 'text-success' : 'text-warning';
                                                        ?>
                                                        <tr>
                                                          <td style="text-align: center;">
                                                            <?php echo $Arm2->Ubicacion; ?>
                                                          </td>
                                                          <td style="text-align: center;"
                                                            class="<?php echo $estatusClass; ?>">
                                                            <strong>
                                                              <?php echo $estatusTexto; ?>
                                                            </strong>
                                                          </td>
                                                          <td style="text-align: center;">
                                                            <form action="RevisionDetalle.php" method="GET">
                                                              <input type="hidden" id="id" name="id"
                                                                value="<?php echo $IdRevision; ?>">
                                                              <input type="hidden" id="ubicacion" name="ubicacion"
                                                                value="<?php echo $Arm2->Ubicacion; ?>">
                                                              <input type="hidden" id="UbicacionNom" name="UbicacionNom"
                                                                value="<?php echo $Arm2->IdUbicacion; ?>">
                                                              <button class="btn btn-sm btn-detalle text-white" type="submit">
                                                                <i class="fa fa-eye"></i> Detalle
                                                              </button>
                                                            </form>
                                                          </td>
                                                        </tr>
                                                        <?php
                                                      }
                                                      ?>
                                                    </tbody>
                                                  </table>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->TipoRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->Descripcion; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaInicio; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaFinal; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->EstatusTexto; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <button type="button" class="btn-editar btn btn-warning btn-sm"
                                                    data-id="<?php echo $IdRevision = $Revision->IdRevision; ?>">
                                                    <i class="fa fa-pen"></i> Editar
                                                  </button>
                                                  <button type="button" class="btn-eliminar btn btn-danger btn-sm"
                                                    data-id="<?php echo $IdRevision = $Revision->IdRevision; ?>">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                  </button>
                                                  <?php if ($todasUbicacionesCompletadas): ?>
                                                    <button type="button" class="btn-cerrar btn btn-success btn-sm"
                                                      data-id="<?php echo $IdRevision = $Revision->IdRevision; ?>">
                                                      <i class="fa fa-lock"></i> Cerrar
                                                    </button>
                                                  <?php else: ?>
                                                    <button type="button" class="btn btn-secondary btn-sm" disabled
                                                      title="Complete todas las ubicaciones para cerrar">
                                                      <i class="fa fa-lock"></i> Cerrar
                                                    </button>
                                                  <?php endif; ?>
                                                </td>
                                              </tr>
                                              <?php
                                            }
                                          }
                                          ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </section>
                                </div>

                                <!-- Pestaña Cerrada -->
                                <div class="tab-pane fade" id="cerrada" role="tabpanel" aria-labelledby="cerrada-tab">
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="dataTableCerrada">
                                        <thead>
                                          <tr>
                                            <th width="auto" style="text-align: center;">IdRevision</th>
                                            <th width="auto" style="text-align: center;">Ubicaciones</th>
                                            <th width="auto" style="text-align: center;">TipoRevision</th>
                                            <th width="auto" style="text-align: center;">Descripción</th>
                                            <th width="auto" style="text-align: center;">Fecha Inicio</th>
                                            <th width="auto" style="text-align: center;">Fecha Final</th>
                                            <th width="auto" style="text-align: center;">Estatus</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                          foreach ($Revisiones as $Revision) {
                                            if ($Revision->Estatus == 2) {
                                              $IdRevision = $Revision->IdRevision;
                                              ?>
                                              <tr>
                                                <td style="text-align: center;">
                                                  <?php echo $IdRevision = $Revision->IdRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php
                                                  $sentArmado2 = $Conexion->query("SELECT t2.Ubicacion,t1.IdUbicacion FROM dbo.t_revisionUbicaciones AS t1
                                              INNER JOIN dbo.t_ubicacion AS t2 ON t2.IdUbicacion = t1.IdUbicacion
                                              WHERE t1.IdRevision=$IdRevision ORDER BY t1.IdUbicacion");
                                                  $Armados2 = $sentArmado2->fetchAll(PDO::FETCH_OBJ);
                                                  ?>
                                                  <table class="table table-bordered table-striped"
                                                    style="background-color:white;">
                                                    <thead>
                                                      <tr>
                                                        <th style="text-align: center;">Ubicacion</th>
                                                        <th style="text-align: center;">Acción</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <?php
                                                      foreach ($Armados2 as $Arm2) {
                                                        ?>
                                                        <tr>
                                                          <td style="text-align: center;">
                                                            <?php echo $Arm2->Ubicacion; ?>
                                                          </td>
                                                          <td style="text-align: center;">
                                                            <form action="RevisionDetalle.php" method="GET">
                                                              <input type="hidden" id="id" name="id"
                                                                value="<?php echo $IdRevision; ?>">
                                                              <input type="hidden" id="ubicacion" name="ubicacion"
                                                                value="<?php echo $Arm2->Ubicacion; ?>">
                                                              <input type="hidden" id="UbicacionNom" name="UbicacionNom"
                                                                value="<?php echo $Arm2->IdUbicacion; ?>">
                                                              <button class="btn btn-sm btn-detalle text-white" type="submit">
                                                                <i class="fa fa-eye"></i> Detalle
                                                              </button>
                                                            </form>
                                                          </td>
                                                        </tr>
                                                        <?php
                                                      }
                                                      ?>
                                                    </tbody>
                                                  </table>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->TipoRevision; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->Descripcion; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaInicio; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->FechaFinal; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                  <?php echo $Revision->EstatusTexto; ?>
                                                </td>
                                              </tr>
                                              <?php
                                            }
                                          }
                                          ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </section>
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
</body>

</html>

<div id="modal-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script type="text/javascript">
  $(document).ready(function () {
    $('#dataTableCreada').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
      }
    });
    $('#dataTableRevision').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
      }
    });
    $('#dataTableCerrada').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
      }
    });

    $(document).on('click', '.btn-agregar', function () {
      $('#modal-container').load('ProcesoRevision/Agregar.php', function () {
        $('#nuevaRevision').modal('show');
        $(document).off('click.modal-close').on('click.modal-close',
          '[data-dismiss="modal"], .btn-close, .modal-close',
          function () {
            $('#nuevaRevision').modal('hide');
          }
        );
      });
    });

    $(document).on('click', '.btn-editar', function () {
      var id = $(this).data('id');
      $('#modal-container').load('ProcesoRevision/Modificar.php?IdRevision=' + id, function () {
        $('#ModificarRevision').modal('show');

        $(document).off('click.modal-close').on('click.modal-close',
          '[data-dismiss="modal"], .btn-close, .modal-close',
          function () {
            $('#ModificarRevision').modal('hide');
          }
        );
      });
    });

    $(document).on('click', '.btn-eliminar', function () {
      var id = $(this).data('id');
      $('#modal-container').load('ProcesoRevision/Eliminar.php?IdRevision=' + id, function () {
        $('#EliminarRevision').modal('show');

        $(document).off('click.modal-close').on('click.modal-close',
          '[data-dismiss="modal"], .btn-close, .modal-close',
          function () {
            $('#EliminarRevision').modal('hide');
          }
        );
      });
    });

    $(document).on('click', '.btn-cerrar', function () {
      var id = $(this).data('id');
      $('#modal-container').load('ProcesoRevision/Cerrar.php?IdRevision=' + id, function () {
        $('#CerrarRevision').modal('show');

        $(document).off('click.modal-close').on('click.modal-close',
          '[data-dismiss="modal"], .btn-close, .modal-close',
          function () {
            $('#CerrarRevision').modal('hide');
          }
        );
      });
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
      $(this).remove();
    });
  });
</script>

<?php
if (isset($_POST['Mov'])) {
  switch ($_POST['Mov']) {
    case 'AgregarRevision':
      AgregarRevision();
      break;
    case 'ModificarRevision':
      ModificarRevision();
      break;
    case 'EliminarRevision':
      EliminarRevision();
      break;
    case 'CerrarRevision':
      CerrarRevision();
      break;
  }
}

function AgregarRevision()
{
  $rutaServidor = getenv('DB_HOST');
  $nombreBaseDeDatos = getenv('DB');
  $usuario = getenv('DB_USER');
  $contraseña = getenv('DB_PASS');

  try {
    $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    date_default_timezone_set('America/Guatemala');

    $fecha = date('Ymd');
    $fechahora = date('Ymd H:i:s');
    $usuario = (!empty($_POST['IdUsuario'])) ? $_POST['IdUsuario'] : NULL;
    ///Encabezados///
    $IdRevision = (!empty($_POST['IdRevision'])) ? $_POST['IdRevision'] : NULL;
    $TipoRevision = (!empty($_POST['TipoRevision'])) ? $_POST['TipoRevision'] : NULL;
    $Descripcion = (!empty($_POST['Descripcion'])) ? $_POST['Descripcion'] : NULL;
    $FechaInicio = (!empty($_POST['FechaInicio'])) ? $_POST['FechaInicio'] : NULL;
    $FechaFinal = (!empty($_POST['FechaFinal'])) ? $_POST['FechaFinal'] : NULL;

    //Lineas//
    $Ubicaciones = (!empty($_POST['Ubicaciones'])) ? $_POST['Ubicaciones'] : NULL;
    $Cantidad2 = count($Ubicaciones);


    $consulta5 = "INSERT INTO t_Revision (IdRevision ,TipoRevision ,Descripcion ,FechaInicio ,FechaFinal ,Estatus)
        VALUES ($IdRevision,$TipoRevision,$Descripcion ,$FechaInicio,$FechaFinal,0)";

    $sentencia5 = $Conexion->prepare("INSERT INTO t_Revision (IdRevision ,TipoRevision ,Descripcion ,FechaInicio ,FechaFinal ,Estatus)
        VALUES (?,?,?,?,?,?);");

    $resultada5 = $sentencia5->execute([$IdRevision, $TipoRevision, $Descripcion, $FechaInicio, $FechaFinal, 0]);

    if ($resultada5) {
      $lineaCounter = 1;
      for ($i = 0; $i < $Cantidad2; $i++) {
        $consulta2 = "INSERT INTO t_revisionUbicaciones (IdRevision,IdUbicacion) VALUES (?,?)";
        $sentencia2 = $Conexion->prepare($consulta2);
        $resultado2 = $sentencia2->execute([$IdRevision, $Ubicaciones[$i]]);

        if (!$resultado2) {
          throw new Exception("Error al insertar la ubicación en la revisión");
        }

        $lineaCounter++;
      }
      if ($resultado2) {

        $sentencia3 = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
        $resultado3 = $sentencia3->execute(['t_revision', 'Agregar Revision' . $IdRevision, $fechahora, "$consulta5", $usuario]);

        $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
        $resultado = $sentencia->execute(['t_revisionUbicaciones', 'Agregar Ubicaciones' . $IdRevision, $fechahora, "$consulta2", $usuario]);

        echo "
              <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                  document.addEventListener('DOMContentLoaded',function(){
                    Swal.fire({
                          icon: 'success',
                          title: 'Se ha dado de Alta Correctamente',
                          showConfirmButton: false
                          }).then(function() {
                          window.location =  'Index.php';
                    });
                  });
              </script>";
      } else {
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded',function(){
                  Swal.fire({
                  icon: 'error',
                  title: 'Algo ha salido mal, intenta de nuevo',
                  showConfirmButton: false,
                  }).then(function() {
                   window.location =  'Index.php';
                    });
                });
            </script>";
      }
    }

  } catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
  } finally {
    $conexion = null;
  }

}

function ModificarRevision()
{
  $rutaServidor = getenv('DB_HOST');
  $nombreBaseDeDatos = getenv('DB');
  $usuario = getenv('DB_USER');
  $contraseña = getenv('DB_PASS');

  try {
    $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    date_default_timezone_set('America/Guatemala');

    $fecha = date('Ymd');
    $fechahora = date('Ymd H:i:s');
    $usuario = (!empty($_POST['IdUsuario'])) ? $_POST['IdUsuario'] : NULL;
    ///Encabezados///
    $IdRevision = (!empty($_POST['IdRevision'])) ? $_POST['IdRevision'] : NULL;
    $TipoRevision = (!empty($_POST['TipoRevision'])) ? $_POST['TipoRevision'] : NULL;
    $Descripcion = (!empty($_POST['Descripcion'])) ? $_POST['Descripcion'] : NULL;
    $FechaInicio = (!empty($_POST['FechaInicio'])) ? $_POST['FechaInicio'] : NULL;
    $FechaFinal = (!empty($_POST['FechaFinal'])) ? $_POST['FechaFinal'] : NULL;

    $consulta2 = "UPDATE t_revision SET TipoRevision = $TipoRevision, Descripcion = $Descripcion ,FechaInicio = $FechaInicio ,FechaFinal = $FechaFinal WHERE  IdRevision =  $IdRevision;";

    $sentencia2 = $Conexion->prepare("UPDATE t_revision SET TipoRevision = ?, Descripcion = ? ,FechaInicio = ? ,FechaFinal = ? WHERE  IdRevision = ?;");

    $resultado2 = $sentencia2->execute([$TipoRevision, $Descripcion, $FechaInicio, $FechaFinal, $IdRevision]);

    if ($resultado2) {
      $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
      $resultado = $sentencia->execute(['t_revision', 'Modificar ' . $IdRevision, $fechahora, "$consulta2", $usuario]);

      echo "
              <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                  document.addEventListener('DOMContentLoaded',function(){
                    Swal.fire({
                          icon: 'success',
                          title: 'Se ha Modificado Correctamente',
                          showConfirmButton: false,
                          }).then(function() {
                          window.location =  'Index.php';
                    });
                  });
              </script>";
    } else {
      echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded',function(){
                  Swal.fire({
                  icon: 'error',
                  title: 'Algo ha salido mal, intenta de nuevo',
                  showConfirmButton: false,
                  }).then(function() {
                   window.location =  'Index.php';
                    });
                });
            </script>";
    }
  } catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
  } finally {
    $conexion = null;
  }
}

function EliminarRevision()
{
  $rutaServidor = getenv('DB_HOST');
  $nombreBaseDeDatos = getenv('DB');
  $usuario = getenv('DB_USER');
  $contraseña = getenv('DB_PASS');

  try {
    $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    date_default_timezone_set('America/Guatemala');

    $fecha = date('Ymd');
    $fechahora = date('Ymd H:i:s');
    $usuario = (!empty($_POST['user'])) ? $_POST['user'] : NULL;
    ///Encabezados///
    $IdRevision = (!empty($_POST['IdRevision'])) ? $_POST['IdRevision'] : NULL;

    $consulta2 = "DELETE FROM t_revision where IdRevision=$IdRevision";
    $sentencia2 = $Conexion->prepare("DELETE FROM t_revision where IdRevision=?;");
    $resultado2 = $sentencia2->execute([$IdRevision]);

    $sentencia3 = $Conexion->prepare("DELETE FROM t_revisionUbicaciones where IdRevision=?;");
    $resultado3 = $sentencia3->execute([$IdRevision]);

    $sentencia3 = $Conexion->prepare("DELETE FROM t_lecturaQR where IdRevision=?;");
    $resultado3 = $sentencia3->execute([$IdRevision]);

    if ($resultado3) {
      $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
      $resultado = $sentencia->execute(['t_revision', 'Eliminar Revision ' . $IdRevision, $fechahora, $consulta2, $usuario]);

      echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script language='JavaScript'>
                      document.addEventListener('DOMContentLoaded',function(){
                        Swal.fire({
                              icon: 'success',
                              title: 'Se ha Eliminado Correctamente',
                              showConfirmButton: false,
                              }).then(function() {
                              window.location = 'Index.php';
                        });
                           });
                        </script>";
    } else {
      echo "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <script language='JavaScript'>
                    document.addEventListener('DOMContentLoaded',function(){
                      Swal.fire({
                            icon: 'error',
                            title: 'Algo ha salido mal, intenta de nuevo',
                            showConfirmButton: false,
                            }).then(function() {
                            window.location = 'Index.php';
                      });
                         });
                      </script>";
    }
  } catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
  } finally {
    $conexion = null;
  }
}

function CerrarRevision()
{
  $rutaServidor = getenv('DB_HOST');
  $nombreBaseDeDatos = getenv('DB');
  $usuario = getenv('DB_USER');
  $contraseña = getenv('DB_PASS');

  try {
    $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    date_default_timezone_set('America/Guatemala');
    $fechahora = date('Y-m-d H:i:s');

    $IdRevision = $_POST['IdRevision'] ?? '';
    $comentario = $_POST['comentario'] ?? '';

    $consulta = "UPDATE t_Revision SET Estatus = 2, comentarios=? WHERE IdRevision = ?";
    $sentencia = $Conexion->prepare($consulta);
    $resultado = $sentencia->execute([$comentario, $IdRevision]);

    if ($resultado) {
      // Registrar en bitácora
      $consultaBitacora = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) VALUES (?,?,?,?,?)";
      $sentenciaBitacora = $Conexion->prepare($consultaBitacora);
      $resultadoBitacora = $sentenciaBitacora->execute([
        't_Revision',
        'Cerrar Revisión ' . $IdRevision,
        $fechahora,
        $consulta,
        $_POST['user'] ?? 'Sistema'
      ]);

      echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Revisión cerrada correctamente',
                            showConfirmButton: false
                        }).then(function() {
                            window.location = 'Index.php';
                        });
                    });
                </script>";
    } else {
      throw new Exception("Error al cerrar la revisión");
    }

  } catch (PDOException $e) {
    echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al cerrar la revisión: " . addslashes($e->getMessage()) . "',
                        showConfirmButton: false
                    }).then(function() {
                        window.location = 'Index.php';
                    });
                });
            </script>";
  } finally {
    $Conexion = null;
  }
}
?>