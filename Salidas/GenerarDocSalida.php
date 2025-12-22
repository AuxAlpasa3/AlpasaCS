<?php
    Include_once "../templates/head.php";
?>
<style>
    .highlighted td {
    background: #ffa500;
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
                  <h1 class="card-title">SOLICITUD DE SALIDA</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                       <?php
                       $sentSalidas = $Conexion->query("SELECT CONCAT('ALPSV-SAL-',t1.IdTarja) AS IdTarja,concat('ALP-',t1.CodBarras) AS CodBarras, CONVERT(DATE,t1.FechaSalida) as FechaSalida, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo, trim(Concat(t2.Material,t2.Shape)) as MaterialShape, t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.IdUbicacion, t3.Ubicacion,t1.EstadoMercancia,t1.Origen,t1.Cliente,t4.NombreCliente,t1.IdRemision,t1.IdLinea
                          FROM t_salida as t1 
                          INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                          INNER JOIN t_ubicacion as t3 on t1.IdUbicacion=t3.IdUbicacion
                          INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente order by t1.IdRemision, t1.IdLinea");
                          $Salidas = $sentSalidas->fetchAll(PDO::FETCH_OBJ);
                      ?>
                  <div class="row">
                    <div class="col-12">
                      <button type="button" data-toggle="modal" data-target="#nuevaRemision" class="btn btn-primary btn-g" style="background-color:#d94f00; border-color:#d94f00;"><i class="fa fa-plus"></i> Añadir Nuevo</button>
                       <?php  include "ProcesoSalida/Agregar.php"; ?> 
                          <section class="pt-2">
                            <div class="table-responsive">
                              <table class="table table-bordered  table-striped" id="dataTable" > 
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
                                   <th width="auto" style="color:black; text-align: center;">Ubicacion</th>
                                   <th width="auto" style="color:black; text-align: center;">Estado Material</th>
                                   <th width="auto" style="color:black; text-align: center;">Origen</th>
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
                                      <?php echo $Salida->IdTarja;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $CodBarras=$Salida->CodBarras;?>    
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
                                      <?php echo $Salida->Ubicacion;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->EstadoMercancia;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->Origen;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->NombreCliente;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->IdRemision;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <button type="button" data-toggle="modal" data-target="#ModificarRemision_<?php echo $CodBarras?>" class="btn btn-warning"><i class="fa fa-pen"></i></button>

                                      <button type="button" data-toggle="modal" data-target="#EliminarRemision_<?php echo $CodBarras;?>" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </td>
                                  </tr>
                                  <?php  
                                      include "ProcesoSalida/Eliminar.php";
                                      include "ProcesoSalida/Modificar.php";
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

   <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable();
        } );
   </script>
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

      <?php
            if(isset($_POST['Mov']))
            {
                switch($_POST['Mov'])
                {
                  case 'AgregarSalida':
                    AgregarSalida();
                  break;
                   case 'ModificarSalida':
                    ModificarSalida();
                  break;
                   case 'EliminarSalida':
                    EliminarSalida();
                  break;
                }
            }

            function AgregarSalida()
            {
              $rutaServidor="192.168.10.195";
              $BaseDeDatos="SYS_ALPASA";
              $user="sa";
              $contraseña = "Alpasa24_";
                try 
                {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$BaseDeDatos", $user, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (Exception $e) { echo "Ocurrió un error con la base de datos: " . $e->getMessage();  } 

                date_default_timezone_set('America/Guatemala');

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                 ///Encabezados///
                $IdRemision = (!empty($_POST['IdRemision']))   ?  $_POST['IdRemision']: NULL;
                $Cliente = (!empty($_POST['Cliente']))   ?  $_POST['Cliente']: NULL;
                $TipoRemision = (!empty($_POST['TipoRemision']))   ?  $_POST['TipoRemision']: NULL;
                $Fecha = (!empty($_POST['Fecha']))   ?  $_POST['Fecha']: NULL;
                $Transportista = (!empty($_POST['Transportista']))   ?  $_POST['Transportista']: NULL;
                $Placas = (!empty($_POST['Placas']))   ?  $_POST['Placas']: NULL;
                $Chofer = (!empty($_POST['Chofer']))   ?  $_POST['Chofer']: NULL;

                //Lineas//
                $Articulo = (!empty($_POST['Articulo']))   ?  $_POST['Articulo']: NULL;
                $Cantidad = (!empty($_POST['Cantidad']))   ?  $_POST['Cantidad']: NULL;
                $Piezas = (!empty($_POST['Piezas']))   ?  $_POST['Piezas']: NULL;

                $Cantidad2= count($Articulo);

                $consulta5="INSERT INTO t_remision_encabezado (IdRemision, Cliente, Transportista, Placas, Chofer, FechaRemision, TipoRemision, Cantidad, FechaRegistro, Estatus) VALUES ( $IdRemision,$Cliente,$Transportista ,$Placas,$Chofer,$Fecha,$TipoRemision,$Cantidad,$fechahora,0)";

                $sentencia5 = $Conexion->prepare("INSERT INTO t_remision_encabezado (IdRemision, Cliente, Transportista, Placas, Chofer, FechaRemision, TipoRemision, Cantidad, FechaRegistro, Estatus) VALUES (?,?,?,?,?,?,?,?,?,?);");

                $resultada5 = $sentencia5->execute([$IdRemision,$Cliente,$Transportista ,$Placas,$Chofer,$Fecha,$TipoRemision,$Cantidad2,$fechahora,0]);

                if($resultada5)
                {   

                  for ($i = 0; $i <  $Cantidad2; $i++) {

                    $consulta2="INSERT INTO t_remision_linea (IdRemision ,IdLinea ,IdArticulo ,Cantidad ,Piezas) VALUES ($IdRemision,$i+1,$Articulo[$i],$Cantidad[$i],$Piezas[$i])";

                    $sentencia2 = $Conexion->prepare("INSERT INTO t_remision_linea (IdRemision ,IdLinea ,IdArticulo ,Cantidad ,Piezas) VALUES (?,?,?,?,?);");

                    $resultado2 = $sentencia2->execute([$IdRemision,$i+1,$Articulo[$i],$Cantidad[$i],$Piezas[$i]]);
                  }

                  if($resultado2)
                  {   

                    $sentencia3 = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado3 = $sentencia3->execute(['t_remision_encabezado','Agregar Remision'.$IdRemision,$fechahora,"$consulta5",$usuario]);

                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_remision_linea','Agregar Detalle Remision'.$IdRemision,$fechahora,"$consulta2",$usuario]);   
                                
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
                                  window.location =  'Index.php';
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
                           window.location =  'Index.php';
                            });
                        });
                    </script>";
                  }
                }
            }

            function ModificarSalida()
            {   
              $rutaServidor="192.168.10.195";
              $BaseDeDatos="SYS_ALPASA";
              $user="sa";
              $contraseña = "Alpasa24_";
                try 
                {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$BaseDeDatos", $user, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (Exception $e) { echo "Ocurrió un error con la base de datos: " . $e->getMessage();  } 

                date_default_timezone_set('America/Guatemala');

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                 ///Encabezados///
                $IdRemision = (!empty($_POST['IdRemision']))   ?  $_POST['IdRemision']: NULL;
                $Cliente = (!empty($_POST['Cliente']))   ?  $_POST['Cliente']: NULL;
                $TipoRemision = (!empty($_POST['TipoRemision']))   ?  $_POST['TipoRemision']: NULL;
                $Fecha = (!empty($_POST['Fecha']))   ?  $_POST['Fecha']: NULL;
                $Transportista = (!empty($_POST['Transportista']))   ?  $_POST['Transportista']: NULL;
                $Placas = (!empty($_POST['Placas']))   ?  $_POST['Placas']: NULL;
                $Chofer = (!empty($_POST['Chofer']))   ?  $_POST['Chofer']: NULL;

                
                  $consulta2="UPDATE t_remision_encabezado SET Cliente = $Cliente, Transportista = $Transportista ,Placas = $Placas ,Chofer = $Chofer ,FechaRemision = $Fecha ,TipoRemision = $TipoRemision WHERE  IdRemision =  $IdRemision;";

                  $sentencia2 = $Conexion->prepare("UPDATE t_remision_encabezado SET Cliente = ?, Transportista = ? ,Placas = ? ,Chofer = ? ,FechaRemision = ? ,TipoRemision = ? WHERE  IdRemision = ?;");

                  $resultado2 = $sentencia2->execute([$Cliente,$Transportista,$Placas ,$Chofer, $Fecha,$TipoRemision, $IdRemision]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_remision_encabezado','Modificar '.$IdRemision, $fechahora,"$consulta2",$usuario]);   
                                 
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
                                  window.location =  'Index.php';
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
                           window.location =  'Index.php';
                            });
                        });
                    </script>";
                  }
            }
            function EliminarSalida()
            { 
              $rutaServidor="192.168.10.195";
              $BaseDeDatos="SYS_ALPASA";
              $user="sa";
              $contraseña = "Alpasa24_";
                try 
                {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$BaseDeDatos", $user, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (Exception $e) { echo "Ocurrió un error con la base de datos: " . $e->getMessage();  } 

                date_default_timezone_set('America/Guatemala');

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                 ///Encabezados///
                $IdRemision = (!empty($_POST['IdRemision']))   ?  $_POST['IdRemision']: NULL;

                $consulta2="DELETE FROM t_remision_encabezado where IdRemision=$IdRemision";

                $sentencia2 = $Conexion->prepare("DELETE FROM t_remision_encabezado where IdRemision=?;");

                 $resultado2 = $sentencia2->execute([$IdRemision]);

                    if($resultado2)
                    {   
                            $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                            $resultado = $sentencia->execute(['t_remision_encabezado','Eliminar Remision '.$IdRemision,$fechahora,$consulta2,$usuario]);   
                        
                            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                              <script language='JavaScript'>
                              document.addEventListener('DOMContentLoaded',function(){
                                Swal.fire({
                                      icon: 'success',
                                      title: 'Se ha Eliminado Correctamente',
                                      showConfirmButton: false,
                                       timer: 500
                                      }).then(function() {
                                      window.location = 'Index.php';
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
                                    window.location = 'Index.php';
                              });
                                 });
                              </script>";
                    }
            }
      ?>