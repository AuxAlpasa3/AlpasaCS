<?php
    Include_once "../templates/head.php";
?>
<style>
    .highlighted td {
    background: #c3c3c3;
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
                  <h1 class="card-title">SALIDAS INTERNO</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                    <div class="col-12">
                     <form name="EnviarSAP" id="EnviarSAP" action="" method="POST" enctype="multipart/form-data">
                           <button type="submit" name="Mov" id="Mov" class="btn btn-success" value="EnviarSAP" disabled>Enviar a SAP</button>
                          <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                          
                      <?php
                       $sentSalidas = $Conexion->query("SELECT t1.IdTarja AS IdTarja,t1.IdTarja AS IdTarjaNum, t1.CodBarras AS CodBarras, t1.CodBarras AS CodBarrasNum, 
                          CONVERT(DATE,t1.FechaSalida) as FechaSalida, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo, trim(Concat(t2.Material,t2.Shape)) as MaterialShape, 
                          t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.Cliente,t4.NombreCliente,t7.IdRemision,t1.IdLinea,t1.Transportista,t1.Placas,t1.Chofer,
                          t1.Checador,t1.Supervisor,t6.NumRecinto
                          FROM t_salida as t1 
                          INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                          INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente 
                          INNER JOIN t_usuario_almacen as t5 on t1.Almacen=t5.IdAlmacen
                          INNER JOIN t_almacen as t6 on t1.Almacen=t6.IdAlmacen
                          INNER JOIN t_remision_encabezado as t7 on t1.IdRemision=t7.IdRemisionEncabezado
                          WHERE t1.ESTATUS IN (0,1,2,3) and t5.IdUsuario=$IdUsuario order by t1.IdRemision, t1.IdLinea");
                          $Salidas = $sentSalidas->fetchAll(PDO::FETCH_OBJ);
                      ?>
                  <div class="row">
                    <div class="col-12">
                          <section class="pt-2">
                            <div class="table-responsive">
                              <table class="table table-bordered  table-striped" id="dataTable" > 
                                <thead>
                                  <tr>
                                   <th width="auto" style="color:black; text-align: center;">   <input type="checkbox"  onClick="toggle(this)"></th>
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
                                   <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                   <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                   <th width="auto" style="color:black; text-align: center;"></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                     foreach($Salidas as $Salida){
                                      $CodBarras=$Salida->CodBarras;
                                      ?>
                                  <tr>
                                    <td width="auto" style="text-align: center;">
                                        <input type="checkbox" name="marcar[]"  value="<?php echo $Salida->CodBarrasNum; ?>">
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                       <?php echo 'ALP'.$Salida->NumRecinto.'-SAL-'.sprintf("%04d", $Salida->IdTarja);?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php  echo $Salida->NumRecinto."-". sprintf("%06d", $CodBarras=$Salida->CodBarras);?>
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
                                      <?php echo $Salida->NombreCliente;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                      <?php echo $Salida->IdRemision;?>
                                    </td>
                                    <td width="auto" style="text-align: center;">
                                        <button type="button" 
                                                class="btn-editar btn btn-warning" 
                                                data-id="<?php echo $Salida->CodBarras;?>">
                                            <i class="fa fa-pen"></i>
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
  
  <!-- Contenedor para el modal (AÑADIDO) -->
  <div id="modal-container"></div>
  
</body>
</html>

<script type="text/javascript">
    function verificarCheckboxes() {
        const checkboxes = document.getElementsByName('marcar[]');
        const btnEnviarSAP = document.getElementById('Mov');

        const algunoMarcado = Array.from(checkboxes).some(checkbox => checkbox.checked);
        btnEnviarSAP.disabled = !algunoMarcado;
    }

    function toggle(source) {
        const checkboxes = document.getElementsByName('marcar[]');
        
        for(let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
            
            if (source.checked) {
                checkboxes[i].closest('tr').classList.add('highlighted');
            } else {
                checkboxes[i].closest('tr').classList.remove('highlighted');
            }
        }
        verificarCheckboxes();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.getElementsByName('marcar[]');
        
        for(let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('click', function() {
                if (this.checked) {
                    this.closest('tr').classList.add('highlighted');
                } else {
                    this.closest('tr').classList.remove('highlighted');
                }
                
                verificarCheckboxes();
            });
        }
        
        verificarCheckboxes();
    });

    $(document).ready(function() {
        $(document).on('click', '.btn-editar', function() {
            var id = $(this).data('id');
            
            // Mostrar un indicador de carga mientras se carga el modal
            $('#modal-container').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Cargando...</p></div>');
            
            // Cargar el contenido del modal
            $('#modal-container').load('ProcesoSalida/Salidas.php?CodBarras=' + id, function(response, status, xhr) {
                if (status == "error") {
                    console.error("Error al cargar el modal:", xhr.status, xhr.statusText);
                    $('#modal-container').html('<div class="alert alert-danger">Error al cargar el formulario: ' + xhr.statusText + '</div>');
                } else {
                    // Una vez cargado, mostrar el modal
                    $('#EditarSalida').modal('show');
                    
                    // Configurar el cierre del modal
                    $(document).off('click.modal-close').on('click.modal-close', 
                        '[data-dismiss="modal"], .btn-close, .modal-close', 
                        function() {
                            $('#EditarSalida').modal('hide');
                        }
                    );
                }
            });
        });
        
        // Limpiar el modal cuando se cierre
        $(document).on('hidden.bs.modal', '#EditarSalida', function () {
            $('#modal-container').empty();
        });
    });
</script>
      <?php
            if(isset($_POST['Mov']))
            {
                switch($_POST['Mov'])
                {
                   case 'ModificarSalida':
                    ModificarSalida();
                  break;
                   case 'EnviarSAP': 
                   EnviarSAP();
                  break;
                }
            }

            function ModificarSalida()
            {   
             
              $rutaServidor= getenv('DB_HOST');
              $nombreBaseDeDatos= getenv('DB');
              $usuario= getenv('DB_USER');
              $contraseña = getenv('DB_PASS');

              try {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

              
                date_default_timezone_set('America/Guatemala');

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;

                $IdTarja = (!empty($_POST['IdTarja']))   ?  $_POST['IdTarja']: NULL;
                $IdRemision = (!empty($_POST['IdRemision']))   ?  $_POST['IdRemision']: NULL;
                $CodBarras = (!empty($_POST['CodBarrasNum']))   ?  $_POST['CodBarrasNum']: NULL;
                $IdArticulo = (!empty($_POST['IdArticulo']))   ?  $_POST['IdArticulo']: NULL;
                $FechaProduccion = (!empty($_POST['FechaProduccion']))   ?  $_POST['FechaProduccion']: NULL;
                $FechaSalida = (!empty($_POST['FechaSalida']))   ?  $_POST['FechaSalida']: ULL;
                $Transportista = (!empty($_POST['Transportista']))   ?  $_POST['Transportista']: NULL;
                $Piezas = (!empty($_POST['Piezas']))   ?  $_POST['Piezas']: NULL;
                $NumPedido = (!empty($_POST['NumPedido']))   ?  $_POST['NumPedido']: NULL;
                $NetWeight = (!empty($_POST['NetWeight']))   ?  $_POST['NetWeight']: NULL;
                $GrossWeight = (!empty($_POST['GrossWeight']))   ?  $_POST['GrossWeight']: NULL;

                $Transportista = (!empty($_POST['Transportista']))   ?  $_POST['Transportista']: NULL;
                $Placas = (!empty($_POST['Placas']))   ?  $_POST['Placas']: NULL;
                $Chofer = (!empty($_POST['Chofer']))   ?  $_POST['Chofer']: NULL;
                $Checador = (!empty($_POST['Checador']))   ?  $_POST['Checador']: NULL;
                $Supervisor = (!empty($_POST['Supervisor']))   ?  $_POST['Supervisor']: NULL;
                $Almacen = (!empty($_POST['Almacen']))   ?  $_POST['Almacen']: NULL;

                
                  $consulta2="UPDATE dbo.t_salida SET Piezas =  $Piezas ,FechaSalida = $FechaSalida ,FechaProduccion = $FechaProduccion ,NumPedido = $NumPedido ,NetWeight =$NetWeight ,GrossWeight = $GrossWeight ,Transportista = $Transportista ,Placas = $Placas ,Chofer = $Chofer ,Checador = $Checador ,Supervisor = $Supervisor, Estatus=3  WHERE IdTarja =  $IdTarja ,IdRemision = $IdRemision ,IdArticulo = $IdArticulo ,CodBarras =  $CodBarras and Almacen= $Almacen;";

                  $sentencia2 = $Conexion->prepare("UPDATE t_salida  SET Piezas = ? ,FechaSalida = ? ,FechaProduccion = ? ,NumPedido = ? ,NetWeight = ? ,GrossWeight = ? ,Transportista = ? ,Placas = ? ,Chofer = ? ,Checador = ? ,Supervisor = ? , Estatus= ? WHERE IdTarja = ?   and IdRemision = ?  and IdArticulo = ?  and CodBarras = ? and Almacen=?");

                  $resultado2 = $sentencia2->execute([$Piezas, $FechaSalida, $FechaProduccion, $NumPedido, $NetWeight, $GrossWeight, $Transportista, $Placas, $Chofer, $Checador, $Supervisor, 3 ,$IdTarja, $IdRemision , $IdArticulo, $CodBarras,$Almacen]);

                  if($resultado2)
                  {   
                    $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                    $resultado = $sentencia->execute(['t_salida','Modificar '.$CodBarras, $fechahora,"$consulta2",$usuario]);   
                                 
                    echo "
                      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                        <script language='JavaScript'>
                          document.addEventListener('DOMContentLoaded',function(){
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Se ha Modificado Correctamente',
                                  showConfirmButton: false,
                                  }).then(function() {
                                  window.location =  'SalidasPendientes.php';
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
                          }).then(function() {
                           window.location =  'SalidasPendientes.php';
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

            function EnviarSAP()
            {

              
              $rutaServidor= getenv('DB_HOST');
              $nombreBaseDeDatos= getenv('DB');
              $usuario= getenv('DB_USER');
              $contraseña = getenv('DB_PASS');

              try {
                  $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
                  $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                date_default_timezone_set('America/Guatemala');

                $fecha = date('Ymd');
                $fechahora = date('Ymd H:i:s');
                $usuario = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
                $marcar[] = (!empty($_POST['marcar[]']))   ?  $_POST['marcar[]']: NULL;

                 if (is_array($_POST['marcar'])) 
                  {
                      $num_countries = count($_POST['marcar']);
                      $current = 0;
                      foreach ($_POST['marcar'] as $key => $value) 
                      {
                          if ($current != $num_countries)
                          {
                             $qry2 = $Conexion->query("SELECT count(*) AS Completo,Almacen from t_salida where CodBarras=$value  and Piezas IS NOT NULL and FechaProduccion IS NOT NULL and NumPedido IS NOT NULL and NetWeight IS NOT NULL and GrossWeight IS NOT NULL and Transportista IS NOT NULL and Placas IS NOT NULL and Chofer IS NOT NULL and Checador IS NOT NULL and Supervisor IS NOT NULL and Almacen IS NOT NULL and Estatus<4 group by Almacen;");
                                $DocNot2 = $qry2->fetchAll(PDO::FETCH_OBJ);
                                foreach($DocNot2 as $Not2)
                                {
                                    $Completo=$Not2->Completo;
                                    $Almacen=$Not2->Almacen;

                                    if($Completo>0)
                                    {

                                      $consulta2="UPDATE t_salida set Estatus=4  Horafinal=$fechahora where CodBarras=$value and Almacen=$Almacen;";

                                      $sentencia2 = $Conexion->prepare("UPDATE t_salida set Estatus=?,  Horafinal=? where CodBarras=? and Almacen=?;");

                                      $resultado2 = $sentencia2->execute([4,$fechahora,$value,$Almacen]);

                                       if($resultado2)
                                      {   
                                         $qry6 = $Conexion->query("SELECT  IdRemision from t_remision_linea where CodBarras='$value' and Almacen=$Almacen;");
                                            $DocNot6 = $qry6->fetchAll(PDO::FETCH_OBJ);
                                            foreach($DocNot6 as $Not6)
                                            {
                                              $IdRemision=$Not6->IdRemision;
                                              $consulta6="UPDATE t_remision_Encabezado set Estatus=4  where IdRemision=$IdRemision and Almacen=$Almacen;";

                                      $sentencia6 = $Conexion->prepare("UPDATE t_remision_Encabezado set Estatus=? where IdRemision=? and Almacen=?;");

                                      $resultado6 = $sentencia6->execute([4,$IdRemision,$Almacen]);
                                    }

                                        if($resultado6)
                                        {   
                                          $sentencia = $Conexion->prepare("INSERT INTO t_bitacora (Tabla,Movimiento,Fecha ,Consulta ,Usuario) VALUES (?,?,?,?,?);");
                                          $resultado = $sentencia->execute(['t_salida','EnviarSAP '.$value, $fechahora,"$consulta2",$usuario]);   
                                                       
                                          echo "
                                            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                                              <script language='JavaScript'>
                                                document.addEventListener('DOMContentLoaded',function(){
                                                  Swal.fire({
                                                        icon: 'success',
                                                        title: 'Se ha Enviado a SAP',
                                                        showConfirmButton: false,
                                                        }).then(function() {
                                                        window.location =  'SalidasPendientes.php';
                                                  });
                                                });
                                            </script>";
                                          }
                                        }
                                      }
                                      
                                }
                          }

                             
                          
                      }
                  }
                  } catch (PDOException $e) {
                  echo "Error de conexión: " . $e->getMessage();
              } finally {
                  $conexion = null;
              }

            }
      ?>