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
                  <h1 class="card-title">CATALOGO DE USUARIOS</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                      <?php
                       $sentUsuarios = $Conexion->query("SELECT t1.IdUsuario,t1.Correo,t1.Usuario,t1.NombreColaborador,t1.TipoUsuario as TipoUsuarioNum,t2.TipoUsuario,t1.Contrasenia FROM t_usuario as t1 inner join t_tipoUsuario as t2 on t1.TipoUsuario=t2.IdTipoUsuario  order by IdUsuario asc");
                          $Usuarios = $sentUsuarios->fetchAll(PDO::FETCH_OBJ);
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
                                   <th width="auto" style="color:black; text-align: center;">IdUsuario</th>
                                   <th width="auto" style="color:black; text-align: center;">Usuario</th>
                                   <th width="auto" style="color:black; text-align: center;">Correo</th>
                                   <th width="auto" style="color:black; text-align: center;">Nombre Colaborador</th>
                                   <th width="auto" style="color:black; text-align: center;">Tipo de Usuario</th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($Usuarios as $Usuario){
                                      $IdUsuario=$Usuario->IdUsuario;
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $IdUsuario=$Usuario->IdUsuario;?>    
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Usuario->Usuario;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Usuario->Correo;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Usuario->NombreColaborador;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Usuario->TipoUsuario;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                              class="btn-editar btn btn-warning" 
                                              data-id="<?php echo  $IdUsuario=$Usuario->IdUsuario;?>">
                                          <i class="fa fa-pen"></i>
                                      </button>
                                        <button type="button" 
                                                class="btn-eliminar btn btn-danger" 
                                                data-id="<?php echo $IdUsuario=$Usuario->IdUsuario;?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                                class="btn-contrasenia btn btn-info" 
                                                data-id="<?php echo $IdUsuario=$Usuario->IdUsuario;?>">
                                            <i class="fa fa-cog"></i>
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
            $('#modal-container').load('ProcesosUsuarios/Eliminar.php?IdUsuario=' + id, function() {
                $('#EliminarUsuario').modal('show');
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#EliminarUsuario').modal('hide');
                    }
                );
            });
        });

        $(document).on('click', '.btn-contrasenia', function() {
            var id = $(this).data('id');
            $('#modal-container').load('ProcesosUsuarios/Contrasenia.php?IdUsuario=' + id, function() {
                $('#ContraseniaUsuario').modal('show');
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#ContraseniaUsuario').modal('hide');
                    }
                );
            });
        });
        
        
        $(document).on('click', '.btn-editar', function() {
            var id = $(this).data('id');
            $('#modal-container').load('ProcesosUsuarios/Modificar.php?IdUsuario=' + id, function() {
                $('#ModificarUsuario').modal('show');
                
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#ModificarUsuario').modal('hide');
                    }
                );
            });
        });

        $('.btn-nuevo').click(function() {
            $('#modal-container').load('ProcesosUsuarios/Agregar.php', function() {
                $('#NuevoUsuario').modal('show');
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#NuevoUsuario').modal('hide');
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
                  case 'AgregarUsuario':
                    AgregarUsuario();
                  break;
                   case 'ModificarUsuario':
                    ModificarUsuario();
                  break;
                   case 'EliminarUsuario':
                    EliminarUsuario();
                  break;
                  case 'ModificarPassword':
                    ModificarPassword();
                  break;
                }
            }

            function AgregarUsuario()
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

                $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;
                $Usuarios = (!empty($_POST['Usuario']))   ?  $_POST['Usuario']: NULL;
                $Correo = (!empty($_POST['Correo']))   ?  $_POST['Correo']: NULL;
                $NColaborador = (!empty($_POST['NColaborador']))   ?  $_POST['NColaborador']: NULL;
                $TUsuario = (!empty($_POST['TUsuario']))   ?  $_POST['TUsuario']: NULL;
                $pass1 = (!empty($_POST['pass1']))   ?  $_POST['pass1']: NULL;
                $pass2 = (!empty($_POST['pass2']))   ?  $_POST['pass2']: NULL;


              $passCifrada = password_hash($pass1,PASSWORD_DEFAULT);
              $passCifrada = $passCifrada;

              if($pass1==$pass2)
              {
                
                  $consulta2="INSERT INTO t_usuario (IdUsuario ,Correo ,Usuario ,NombreColaborador ,TipoUsuario ,Contrasenia) VALUES ($IdUsuario,$Correo,$Usuarios, $NColaborador,$TUsuario,$passCifrada)";

                  $sentencia2 = $Conexion->prepare("INSERT INTO t_usuario (IdUsuario ,Correo ,Usuario ,NombreColaborador ,TipoUsuario ,Contrasenia) VALUES (?,?,?,?,?,?)");

                  $resultado2 = $sentencia2->execute([$IdUsuario,$Correo,$Usuarios, $NColaborador,$TUsuario,$passCifrada]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_usuario','Agregar Usuario'.$IdUsuario,$fechahora,"$consulta2",$usuario]);   
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha dado de Alta Correctamente',
                                  showConfirmButton: false
                                  }).then(function() {
                                  window.location =  'Usuarios.php';
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
                           window.location =  'Usuarios.php';
                            });
                        });
                    </script>";
                  }
              }
              else  {
                echo "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                      <script language='JavaScript'>
                        document.addEventListener('DOMContentLoaded',function(){
                          Swal.fire({
                          icon: 'error',
                          title: 'Las Contraseñas no coinciden',
                          showConfirmButton: false
                          }).then(function() {
                           window.location =  'Usuarios.php';
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
            function ModificarUsuario()
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

                $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;
                $Usuarios = (!empty($_POST['Usuario']))   ?  $_POST['Usuario']: NULL;
                $Correo = (!empty($_POST['Correo']))   ?  $_POST['Correo']: NULL;
                $NColaborador = (!empty($_POST['NColaborador']))   ?  $_POST['NColaborador']: NULL;
                $TUsuario = (!empty($_POST['TUsuario']))   ?  $_POST['TUsuario']: NULL;
                $Estatus = (!empty($_POST['Estatus']))   ?  $_POST['Estatus']: NULL;

                  
                  $consulta2="UPDATE t_usuario SET  Correo = $Correo ,Usuario= $Usuarios,NombreColaborador=  $NColaborador,TipoUsuario= $TUsuario, Estatus=$Estatus WHERE IdUsuario=$IdUsuario";

                  $sentencia2 = $Conexion->prepare("UPDATE t_usuario SET  Correo = ? ,Usuario= ?,NombreColaborador=  ?,TipoUsuario= ? , Estatus=? WHERE IdUsuario= ?;");

                  $resultado2 = $sentencia2->execute([ $Correo,$Usuarios, $NColaborador,$TUsuario,$Estatus,$IdUsuario]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_usuario','Modificar Usuario'.$IdUsuario,$fechahora,"$consulta2",$usuario]); 
                                
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
                                  window.location =  'Usuarios.php';
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
                           window.location =  'Usuarios.php';
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
            function EliminarUsuario()
            { 
                if (!isset($_POST['contraseniaConfirmacion']) || empty($_POST['contraseniaConfirmacion'])) {
                    echo "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <script language='JavaScript'>
                        document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de seguridad',
                                text: 'Se requiere confirmación de contraseña para eliminar usuarios',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(function() {
                                window.location = 'Usuarios.php';
                            });
                        });
                    </script>";
                    return;
                }

                $contraseniaIngresada = $_POST['contraseniaConfirmacion'];
                $usuarioActual = $_SESSION['usuario'] ?? null;
                
                if ($usuarioActual) {
                    $sentencia = $Conexion->prepare("SELECT Contrasenia FROM t_usuario WHERE Usuario = ?");
                    $sentencia->execute([$usuarioActual]);
                    $usuario = $sentencia->fetch(PDO::FETCH_OBJ);
                    
                    if (!$usuario || !password_verify($contraseniaIngresada, $usuario->Contrasenia)) {
                        echo "
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                            document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Contraseña incorrecta',
                                    text: 'No se pudo verificar su identidad',
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(function() {
                                    window.location = 'Usuarios.php';
                                });
                            });
                        </script>";
                        return;
                    }
                }
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

                    $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;

                    $consulta2="DELETE FROM t_usuario where IdUsuario=$IdUsuario";

                    $sentencia2 = $Conexion->prepare("DELETE FROM t_usuario where IdUsuario=?;");

                    $resultado2 = $sentencia2->execute([$IdUsuario]);

                    if($resultado2) {   
                        $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                        $resultado = $sentencia->execute(['t_usuario','Eliminar Usuario '.$IdUsuario,$fechahora,$consulta2,$usuario]);   
                    
                        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                            document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Se ha Eliminado Correctamente',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(function() {
                                    window.location = 'Usuarios.php';
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
                                    timer: 1500
                                }).then(function() {
                                    window.location = 'Usuarios.php';
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
            function ModificarPassword()
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

                $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;
                $pass1 = (!empty($_POST['pass1']))   ?  $_POST['pass1']: NULL;
                $pass2 = (!empty($_POST['pass2']))   ?  $_POST['pass2']: NULL;


              $passCifrada = password_hash($pass1,PASSWORD_DEFAULT);
              $passCifrada = $passCifrada;

                if($pass1==$pass2)
                {
                  $consulta2="UPDATE t_usuario SET Contrasenia = $passCifrada WHERE  IdUsuario = $IdUsuario";

                  $sentencia2 = $Conexion->prepare("UPDATE t_usuario SET  Contrasenia  = ? WHERE  IdUsuario = ?;");

                  $resultado2 = $sentencia2->execute([$passCifrada,$IdUsuario]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_usuario','Modificar Contraseña Usuario'.$IdUsuario,$fechahora,"$consulta2",$usuario]); 
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha Modificado Correctamente',
                                  showConfirmButton: false
                                  }).then(function() {
                                  window.location =  'Usuarios.php';
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
                           window.location =  'Usuarios.php';
                            });
                        });
                    </script>";
                  }
                }
                  else  {
                    echo "
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                          <script language='JavaScript'>
                            document.addEventListener('DOMContentLoaded',function(){
                              Swal.fire({
                              icon: 'error',
                              title: 'Las Contraseñas no coinciden',
                              showConfirmButton: false
                              }).then(function() {
                               window.location =  'Usuarios.php';
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