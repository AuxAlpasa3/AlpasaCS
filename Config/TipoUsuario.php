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
                  <h1 class="card-title">SUBCATALOGO DE USUARIOS: <b>TIPOS DE USUARIOS</b></h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                      <?php
                       $sentTUsuario = $Conexion->query("SELECT IdTipoUsuario, TipoUsuario FROM t_tipoUsuario");
                          $tiposusuarios = $sentTUsuario->fetchAll(PDO::FETCH_OBJ);
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
                                   <th width="auto" style="color:black; text-align: center;">IdTipos Usuarios</th>
                                   <th width="auto" style="color:black; text-align: center;">Tipo Usuario</th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($tiposusuarios as $Tipos){
                                      $IdTipoUsuario=$Tipos->IdTipoUsuario;
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $IdTipoUsuario=$Tipos->IdTipoUsuario;?>    
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Tipos->TipoUsuario;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                              class="btn-editar btn btn-warning" 
                                              data-id="<?php echo  $IdTipoUsuario=$Tipos->IdTipoUsuario;?>">
                                          <i class="fa fa-pen"></i>
                                      </button>
                                      <button type="button" 
                                              class="btn-eliminar btn btn-danger" 
                                              data-id="<?php echo $IdTipoUsuario=$Tipos->IdTipoUsuario;?>">
                                          <i class="fa fa-trash"></i>
                                      </button>
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
            $('#modal-container').load('ProcesosTipoUsuario/Eliminar.php?IdTipoUsuario=' + id, function() {
                $('#EliminarTipoUsuario').modal('show');
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#EliminarTipoUsuario').modal('hide');
                    }
                );
            });
        });
        
        $(document).on('click', '.btn-editar', function() {
            var id = $(this).data('id');
            $('#modal-container').load('ProcesosTipoUsuario/Modificar.php?IdTipoUsuario=' + id, function() {
                $('#ModificarTipoUsuario').modal('show');
                
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#ModificarTipoUsuario').modal('hide');
                    }
                );
            });
        });

        $('.btn-nuevo').click(function() {
            $('#modal-container').load('ProcesosTipoUsuario/Agregar.php', function() {
                $('#NuevoTipoUsuario').modal('show');
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#NuevoTipoUsuario').modal('hide');
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
                  case 'AgregarTipoUsuario':
                    AgregarTipoUsuario();
                  break;
                   case 'ModificarTipoUsuario':
                    ModificarTipoUsuario();
                  break;
                   case 'EliminarTipoUsuario':
                    EliminarTipoUsuario();
                  break;
                }
            }

            function AgregarTipoUsuario()
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
                $IdTipoUsuario = (!empty($_POST['IdTipoUsuario']))   ?  $_POST['IdTipoUsuario']: NULL;
                $TipoUsuario = (!empty($_POST['TipoUsuario']))   ?  $_POST['TipoUsuario']: NULL;
                
                  $consulta2=" INSERT INTO t_tipoUsuario (IdTipoUsuario,TipoUsuario) VALUES ($IdTipoUsuario,$TipoUsuario)";

                  $sentencia2 = $Conexion->prepare("INSERT INTO t_tipoUsuario (IdTipoUsuario,TipoUsuario) VALUES (?,?);");

                  $resultado2 = $sentencia2->execute([$IdTipoUsuario,$TipoUsuario]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_tipoUsuario','Agregar tiposusuarios'.$IdTipoUsuario,$fechahora,"$consulta2",$usuario]);   
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha dado de Alta Correctamente',
                                  showConfirmButton: false,
                                  timer: 500
                                  }).then(function() {
                                  window.location =  'TipoUsuario.php';
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
                           window.location =  'TipoUsuario.php';
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
            function ModificarTipoUsuario()
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
                $IdTipoUsuario = (!empty($_POST['IdTipoUsuario']))   ?  $_POST['IdTipoUsuario']: NULL;
                $TipoUsuario = (!empty($_POST['TipoUsuario']))   ?  $_POST['TipoUsuario']: NULL;
                  
                  $consulta2="UPDATE t_tipoUsuario SET TipoUsuario = $TipoUsuario  WHERE  IdTipoUsuario = $IdTipoUsuario";

                  $sentencia2 = $Conexion->prepare("UPDATE t_tipoUsuario SET TipoUsuario = ?  WHERE  IdTipoUsuario = ?;");

                  $resultado2 = $sentencia2->execute([$TipoUsuario, $IdTipoUsuario]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_tipoUsuario','Modificar TipoUsuario'.$IdTipoUsuario,$fechahora,"$consulta2",$usuario]); 
                                
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
                                  window.location =  'TipoUsuario.php';
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
                           window.location =  'TipoUsuario.php';
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
            function EliminarTipoUsuario()
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
              $IdTipoUsuario = (!empty($_POST['IdTipoUsuario']))   ?  $_POST['IdTipoUsuario']: NULL;
              $TipoUsuario = (!empty($_POST['TipoUsuario']))   ?  $_POST['TipoUsuario']: NULL;
                  

                $consulta2="DELETE FROM t_tipoUsuario where IdTipoUsuario=$IdTipoUsuario;";
                echo $consulta2;

                $sentencia2 = $Conexion->prepare("DELETE FROM t_tipoUsuario where IdTipoUsuario=?;");

                 $resultado2 = $sentencia2->execute([$IdTipoUsuario]);

                    if($resultado2)
                    {   
                            $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                            $resultado = $sentencia->execute(['t_tipoUsuario','Eliminar TipoUsuario '.$IdTipoUsuario,$fechahora,$consulta2,$usuario]);   
                        
                            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script language='JavaScript'>
                              document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                      icon: 'success',
                                      title: 'Se ha Eliminado Correctamente',
                                      showConfirmButton: false,
                                      timer: 500
                                      }).then(function() {
                                      window.location = 'TipoUsuario.php';
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
                                    window.location = 'TipoUsuario.php';
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