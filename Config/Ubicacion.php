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
                  <h1 class="card-title">CATALOGO DE UBICACIONES</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                      <?php
                       $sentUbicacionInterna = $Conexion->query("SELECT IdUbicacion, NomCorto, NomLargo, 
Concat(Ciudad,',',Estado,'.',Pais) as Ubicacion From t_ubicacion");
                          $Ubicaciones = $sentUbicacionInterna->fetchAll(PDO::FETCH_OBJ);
                      ?>
                  <div class="row">
                    <div class="col-12">
                      <button type="button" 
                                class="btn-nuevo btn btn-primary btn-g" style="background-color:#d94f00; border-color:#d94f00;"><i class="fa fa-plus"> Añadir Nuevo</i>
                        </button>
                          <section class="pt-2">
                            <div class="table-responsive">
                              <table class="table table-bordered  table-striped" id="dataTable" > 
                                <thead>
                                  <tr>
                                   <th width="auto" style="color:black; text-align: center;">IdUbicacion</th>
                                   <th width="auto" style="color:black; text-align: center;">NomCorto</th>
                                   <th width="auto" style="color:black; text-align: center;">NomLargo</th>
                                   <th width="auto" style="color:black; text-align: center;">Ubicacion</th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($Ubicaciones as $Ubicacion){
                                      $IdUbicacion=$Ubicacion->IdUbicacion;
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $IdUbicacion;?>    
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Ubicacion->NomCorto;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Ubicacion->NomLargo;?>
                                    </td>
                                      <td width="auto" style="text-align: center;">
                                      <?php echo $Ubicacion->Ubicacion;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                              class="btn-editar btn btn-warning" 
                                              data-id="<?php echo $IdUbicacion;?>">
                                          <i class="fa fa-pen"></i>
                                      </button>
                                        <button type="button" 
                                                class="btn-eliminar btn btn-danger" 
                                                data-id="<?php echo $IdUbicacion;?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
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


<div id="modal-container"></div>
<script type="text/javascript">

$(document).ready(function() {
    $(document).on('click', '.btn-eliminar', function() {
        var id = $(this).data('id');
        $('#modal-container').load('ProcesosUbicacion/Eliminar.php?IdUbicacion=' + id, function() {
            $('#EliminarUbicacion').modal('show');
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#EliminarUbicacion').modal('hide');
                }
            );
        });
    });
    
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $('#modal-container').load('ProcesosUbicacion/Modificar.php?IdUbicacion=' + id, function() {
            $('#ModificarUbicacion').modal('show');
            
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#ModificarUbicacion').modal('hide');
                }
            );
        });
    });

    $('.btn-nuevo').click(function() {
        $('#modal-container').load('ProcesosUbicacion/Agregar.php', function() {
            $('#NuevoUbicacion').modal('show');
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#NuevoUbicacion').modal('hide');
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
            if(isset($_POST['Mov']))
            {
                switch($_POST['Mov'])
                {
                  case 'AgregarUbicacion':
                    AgregarUbicacion();
                  break;
                   case 'ModificarUbicacion':
                    ModificarUbicacion();
                  break;
                   case 'EliminarUbicacion':
                    EliminarUbicacion();
                  break;
                }
            }

            function AgregarUbicacion()
            {
               $rutaServidor= getenv('DB_HOST');
              $nombreBaseDeDatos= getenv('DB');
              $usuario= getenv('DB_USER');
              $contraseña = getenv('DB_PASS');

              try {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                
                $ZonaHoraria= getenv('ZonaHoraria');
                date_default_timezone_set($ZonaHoraria);

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                $IdUbicacion = (!empty($_POST['IdUbicacion']))   ?  $_POST['IdUbicacion']: NULL;
                $NomCorto = (!empty($_POST['NomCorto']))   ?  $_POST['NomCorto']: NULL;
                $NomLargo = (!empty($_POST['NomLargo']))   ?  $_POST['NomLargo']: NULL;
                $Ciudad = (!empty($_POST['Ciudad']))   ?  $_POST['Ciudad']: NULL;
                $Estado = (!empty($_POST['Estado']))   ?  $_POST['Estado']: NULL;
                $Pais = (!empty($_POST['Pais']))   ?  $_POST['Pais']: NULL;
                
                  $consulta2=" INSERT INTO t_ubicacion (IdUbicacion,NomCorto,NomLargo,Ciudad,Estado,Pais) VALUES ($IdUbicacion,$NomCorto,$NomLargo,$Ciudad,$Estado,$Pais)";

                  $sentencia2 = $Conexion->prepare("INSERT INTO t_ubicacion (IdUbicacion,NomCorto,NomLargo,Ciudad,Estado,Pais) VALUES (?,?,?,?,?,?);");

                  $resultado2 = $sentencia2->execute([$IdUbicacion,$NomCorto,$NomLargo,$Ciudad,$Estado,$Pais]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_ubicacion','Agregar Ubicacion'.$IdUbicacion,$fechahora,"$consulta2",$usuario]);   
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha dado de Alta Correctamente',
                                  showConfirmButton: false
                                  }).then(function() {
                                  window.location =  'Ubicacion.php';
                            });
                          });
                      </script>";
                  }
                  else
                  {
                    echo "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script language='JavaScript'>
                        document.addEventListener('DOMContentLoaded',function(){
                          Swal.fire({
                          icon: 'error',
                          title: 'Algo ha salido mal, intenta de nuevo',
                          showConfirmButton: false,
                          timer: 500
                          }).then(function() {
                           window.location =  'Ubicacion.php';
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
            function ModificarUbicacion()
            {   
               $rutaServidor= getenv('DB_HOST');
              $nombreBaseDeDatos= getenv('DB');
              $usuario= getenv('DB_USER');
              $contraseña = getenv('DB_PASS');

              try {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                
                $ZonaHoraria= getenv('ZonaHoraria');
                date_default_timezone_set($ZonaHoraria);

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                $IdUbicacion = (!empty($_POST['IdUbicacion']))   ?  $_POST['IdUbicacion']: NULL;
                $NomCorto = (!empty($_POST['NomCorto']))   ?  $_POST['NomCorto']: NULL;
                $NomLargo = (!empty($_POST['NomLargo']))   ?  $_POST['NomLargo']: NULL;
                
                $Ciudad = (!empty($_POST['Ciudad']))   ?  $_POST['Ciudad']: NULL;
                $Estado = (!empty($_POST['Estado']))   ?  $_POST['Estado']: NULL;
                $Pais = (!empty($_POST['Pais']))   ?  $_POST['Pais']: NULL;
                  
                  $consulta2="UPDATE t_ubicacion SET NomCorto = '$NomCorto', NomLargo = '$NomLargo', Ciudad = '$Ciudad', Estado = '$Estado', Pais = '$Pais' WHERE  IdUbicacion = $IdUbicacion";

                  $sentencia2 = $Conexion->prepare("UPDATE t_ubicacion SET NomCorto = ?, NomLargo = ?, Ciudad = ?, Estado = ?, Pais = ? WHERE  IdUbicacion = ?;");

                  $resultado2 = $sentencia2->execute([$NomCorto,$NomLargo,$Ciudad,$Estado,$Pais,$IdUbicacion]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_ubicacion','Modificar Ubicacion'.$IdUbicacion,$fechahora,"$consulta2",$usuario]); 
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha Modificado Correctamente',
                                  showConfirmButton: false,
                                  timer: 500
                                  }).then(function() {
                                  window.location =  'Ubicacion.php';
                            });
                          });
                      </script>";
                  }
                  else
                  {
                    echo "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script language='JavaScript'>
                        document.addEventListener('DOMContentLoaded',function(){
                          Swal.fire({
                          icon: 'error',
                          title: 'Algo ha salido mal, intenta de nuevo',
                          showConfirmButton: false,
                          timer: 500
                          }).then(function() {
                           window.location =  'Ubicacion.php';
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
            function EliminarUbicacion()
            { 
               $rutaServidor= getenv('DB_HOST');
              $nombreBaseDeDatos= getenv('DB');
              $usuario= getenv('DB_USER');
              $contraseña = getenv('DB_PASS');

              try {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                
                $ZonaHoraria= getenv('ZonaHoraria');
                date_default_timezone_set($ZonaHoraria);

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                $IdUbicacion = (!empty($_POST['IdUbicacion']))   ?  $_POST['IdUbicacion']: NULL;

                $consulta2="DELETE FROM t_ubicacion where IdUbicacion=$IdUbicacion";

                $sentencia2 = $Conexion->prepare("DELETE FROM t_ubicacion where IdUbicacion=?;");

                 $resultado2 = $sentencia2->execute([$IdUbicacion]);

                    if($resultado2)
                    {   
                            $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                            $resultado = $sentencia->execute(['t_ubicacion','Eliminar Ubicacion '.$IdUbicacion,$fechahora,$consulta2,$usuario]);   
                        
                            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script language='JavaScript'>
                              document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                      icon: 'success',
                                      title: 'Se ha Eliminado Correctamente',
                                      showConfirmButton: false,
                                      timer: 500
                                      }).then(function() {
                                      window.location = 'Ubicacion.php';
                                });
                                   });
                                </script>";
                    }
                    else
                    {
                           echo "
                            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                            <script language='JavaScript'>
                            document.addEventListener('DOMContentLoaded',function(){
                              Swal.fire({
                                    icon: 'error',
                                    title: 'Algo ha salido mal, intenta de nuevo',
                                    showConfirmButton: false,
                                    timer: 500
                                    }).then(function() {
                                    window.location = 'Ubicacion.php';
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
      ?>