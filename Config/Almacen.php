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
                  <h1 class="card-title">CATALOGO DE ALMACENES</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                      <?php
                       $sentAlmacen = $Conexion->query("SELECT IdAlmacen,Almacen,Ubicacion FROM t_almacen order by IdAlmacen asc");
                          $Almacenes = $sentAlmacen->fetchAll(PDO::FETCH_OBJ);
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
                                   <th width="auto" style="color:black; text-align: center;">Id Almacen</th>
                                   <th width="auto" style="color:black; text-align: center;">Almacen</th>
                                   <th width="auto" style="color:black; text-align: center;">Ubicacion</th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($Almacenes as $Almacen){
                                      $IdAlmacen=$Almacen->IdAlmacen;
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $IdAlmacen=$Almacen->IdAlmacen;?>    
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Almacen->Almacen;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Almacen->Ubicacion;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                              class="btn-editar btn btn-warning" 
                                              data-id="<?php echo $IdAlmacen=$Almacen->IdAlmacen;?>">
                                          <i class="fa fa-pen"></i>
                                      </button>
                                        <button type="button" 
                                                class="btn-eliminar btn btn-danger" 
                                                data-id="<?php echo $IdAlmacen=$Almacen->IdAlmacen;?>">
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
        $('#modal-container').load('ProcesosAlmacen/Eliminar.php?IdAlmacen=' + id, function() {
            $('#EliminarAlmacen').modal('show');
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#EliminarAlmacen').modal('hide');
                }
            );
        });
    });
    
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $('#modal-container').load('ProcesosAlmacen/Modificar.php?IdAlmacen=' + id, function() {
            $('#ModificarAlmacen').modal('show');
            
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#ModificarAlmacen').modal('hide');
                }
            );
        });
    });

    $('.btn-nuevo').click(function() {
        $('#modal-container').load('ProcesosAlmacen/Agregar.php', function() {
            $('#NuevoAlmacen').modal('show');
            $(document).off('click.modal-close').on('click.modal-close', 
                '[data-dismiss="modal"], .btn-close, .modal-close', 
                function() {
                    $('#NuevoAlmacen').modal('hide');
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
                  case 'AgregarAlmacen':
                    AgregarAlmacen();
                  break;
                   case 'ModificarAlmacen':
                    ModificarAlmacen();
                  break;
                   case 'EliminarAlmacen':
                    EliminarAlmacen();
                  break;
                }
            }

            function AgregarAlmacen()
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
                $IdAlmacen = (!empty($_POST['IdAlmacen']))   ?  $_POST['IdAlmacen']: NULL;
                $Almacen = (!empty($_POST['Almacen']))   ?  $_POST['Almacen']: NULL;
                $Ubicacion = (!empty($_POST['Ubicacion']))   ?  $_POST['Ubicacion']: NULL;
                
                  $consulta2=" INSERT INTO t_almacen (IdAlmacen,Almacen,Ubicacion) VALUES ($IdAlmacen,$Almacen, $Ubicacion)";

                  $sentencia2 = $Conexion->prepare("INSERT INTO t_almacen (IdAlmacen,Almacen,Ubicacion) VALUES (?,?,?);");

                  $resultado2 = $sentencia2->execute([$IdAlmacen,$Almacen, $Ubicacion]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_almacen','Agregar Almacen'.$IdAlmacen,$fechahora,"$consulta2",$usuario]);   
                                
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha dado de Alta Correctamente',
                                  showConfirmButton: false
                                  }).then(function() {
                                  window.location =  'Almacen.php';
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
                           window.location =  'Almacen.php';
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
            function ModificarAlmacen()
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
                $IdAlmacen = (!empty($_POST['IdAlmacen']))   ?  $_POST['IdAlmacen']: NULL;
                $Almacen = (!empty($_POST['Almacen']))   ?  $_POST['Almacen']: NULL;
                $Ubicacion = (!empty($_POST['Ubicacion']))   ?  $_POST['Ubicacion']: NULL;
                  
                  $consulta2="UPDATE t_almacen SET Almacen = $Almacen , Ubicacion  = $Ubicacion WHERE  IdAlmacen = $IdAlmacen";

                  $sentencia2 = $Conexion->prepare("UPDATE t_almacen SET Almacen = ? , Ubicacion  = ? WHERE  IdAlmacen = ?;");

                  $resultado2 = $sentencia2->execute([$Almacen,$Ubicacion,$IdAlmacen]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_almacen','Modificar Almacen'.$IdAlmacen,$fechahora,"$consulta2",$usuario]); 
                                
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
                                  window.location =  'Almacen.php';
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
                           window.location =  'Almacen.php';
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
            function EliminarAlmacen()
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
                $IdAlmacen = (!empty($_POST['IdAlmacen']))   ?  $_POST['IdAlmacen']: NULL;

                $consulta2="DELETE FROM t_almacen where IdAlmacen=$IdAlmacen";

                $sentencia2 = $Conexion->prepare("DELETE FROM t_almacen where IdAlmacen=?;");

                 $resultado2 = $sentencia2->execute([$IdAlmacen]);

                    if($resultado2)
                    {   
                            $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                            $resultado = $sentencia->execute(['t_almacen','Eliminar Almacen '.$IdAlmacen,$fechahora,$consulta2,$usuario]);   
                        
                            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script language='JavaScript'>
                              document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                      icon: 'success',
                                      title: 'Se ha Eliminado Correctamente',
                                      showConfirmButton: false,
                                      timer: 500
                                      }).then(function() {
                                      window.location = 'Almacen.php';
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
                                    window.location = 'Almacen.php';
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