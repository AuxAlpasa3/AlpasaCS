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
                  <h1 class="card-title">CANCELAR REMISIONES EN PROCESO</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                           <form name="Cancelar" id="Cancelar" action="" method="POST" enctype="multipart/form-data">
                           <button type="button" name="Mov" id="Mov" class="btn btn-danger" value="Cancelar" disabled onclick="confirmCancel()">Cancelar</button>
                                <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                <input type="hidden" name="Mov" value="Cancelar">
                          
                            <?php
                             $sentCancelar = $Conexion->query("SELECT IdTarja,TipoRemision,TipoRemisionNum,STRING_AGG(IdRemision,',') AS IdRemisiones,NumRecinto,IdAlmacen
                                FROM (
                                    SELECT DISTINCT
                                        COALESCE(t2.IdTarja, t3.IdTarja) AS IdTarja,
                                        t4.TipoRemision,t1.TipoRemision as TipoRemisionNum,
                                        COALESCE(t1.IdRemision, t1.IdRemision) AS IdRemision,T6.NumRecinto,t6.IdAlmacen
                                    FROM t_remision_encabezado AS t1 
                                    LEFT JOIN t_ingreso AS t2 ON t1.IdRemisionEncabezado = t2.IdRemision
                                    LEFT JOIN t_salida AS t3 ON t1.IdRemisionEncabezado = t3.IdRemision
                                    INNER JOIN t_tipoRemision AS t4 ON t1.TipoRemision = t4.IdTipoRemision
                                    INNER JOIN t_usuario_almacen as t5 on t1.almacen=t5.IdAlmacen
                                    INNER JOIN t_almacen as t6 on t1.Almacen=t6.IdAlmacen
                                    WHERE t2.Estatus < 3 OR t3.Estatus < 3 and t5.IdUsuario=$IdUsuario
                                ) AS subquery
                                GROUP BY IdTarja, TipoRemision,TipoRemisionNum,NumRecinto,IdAlmacen");
                                $Cancelar = $sentCancelar->fetchAll(PDO::FETCH_OBJ);
                            ?>

                          <div class="row">
                            <div class="col-12"> 
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="tablaSinB" > 
                                        <thead>
                                          <tr>
                                           <th width="auto" style="color:black; text-align: center;">Seleccionar</th>
                                           <th width="auto" style="color:black; text-align: center;">IdTarja</th>
                                           <th width="auto" style="color:black; text-align: center;">Tipo Remision</th>
                                           <th width="auto" style="color:black; text-align: center;">Id Remisiones</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php foreach($Cancelar as $Cancel): ?>

                                            <tr>
                                                <td width="auto" style="text-align: center;">
                                                    <input type="radio" name="marcar" 
                                                            value="<?php echo $Cancel->IdTarja.'_'.$Cancel->TipoRemisionNum.'_'.$Cancel->IdAlmacen;?>">
                                                </td>
                                                <td width="auto" style="text-align: center;">
                                                <?php 
                                                    if($Cancel->TipoRemisionNum==1) 
                                                    {
                                                        echo 'ALP'.$Cancel->NumRecinto.'-ING-'.sprintf("%04d", $Cancel->IdTarja);
                                                    }
                                                    elseif($Cancel->TipoRemisionNum==2) 
                                                    {
                                                        echo 'ALP'.$Cancel->NumRecinto.'-SAL-'.sprintf("%04d", $Cancel->IdTarja);
                                                    }
                                                ?>
                                                </td> 
                                                <td width="auto" style="text-align: center;">
                                                  <?php echo $Cancel->TipoRemision;?>
                                                </td>
                                                <td width="auto" style="text-align: center;">
                                                  <?php echo $Cancel->IdRemisiones;?>
                                                </td>
                                            </tr>
                                          <?php endforeach; ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </section>
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
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function verificarRadio() {
        const radio = document.querySelector('input[name="marcar"]:checked');
        const btnCancelar = document.getElementById('Mov');
        
        btnCancelar.disabled = !radio;
    }

    function confirmCancel() {
        Swal.fire({
            title: 'Confirmar Cancelación',
            html: `
                <p>¿Estás seguro que deseas cancelar la remisión seleccionada?</p>
                <input type="password" id="password" class="swal2-input" placeholder="Ingresa tu contraseña">
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#password').value;
                if (!password) {
                    Swal.showValidationMessage('Debes ingresar tu contraseña');
                }
                return { password: password }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const userId = document.getElementById('user').value;
                const formData = new FormData();
                formData.append('action', 'verificar_contrasena');
                formData.append('user_id', userId);
                formData.append('password', result.value.password);
                
                fetch('ProcesoRemision/verificar_contrasena.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const passInput = document.createElement('input');
                        passInput.type = 'hidden';
                        passInput.name = 'confirmed_password';
                        passInput.value = result.value.password;
                        document.getElementById('Cancelar').appendChild(passInput);
                        
                        document.getElementById('Cancelar').submit();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Contraseña incorrecta'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al verificar la contraseña'
                    });
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.getElementsByName('marcar');
        
        for(let i = 0; i < radios.length; i++) {
            radios[i].addEventListener('click', function() {
                const allRows = document.querySelectorAll('#tablaSinB tbody tr');
                allRows.forEach(row => row.classList.remove('highlighted'));
                
                if (this.checked) {
                    this.closest('tr').classList.add('highlighted');
                }
                verificarRadio();
            });
        }
        verificarRadio();
    });
</script>

<?php

    if(isset($_POST['Mov']))
    {
        switch($_POST['Mov'])
        {
           case 'Cancelar':
            Cancelar();
          break;
        }
    }

    function Cancelar() 
    {   
        try 
        {
            if (empty($_POST['marcar']) || empty($_POST['confirmed_password'])) {
                mostrarAlerta('error', 'Error', 'No se seleccionó una remisión para cancelar');    
            }

            $rutaServidor = getenv('DB_HOST');
            $nombreBaseDeDatos = getenv('DB');
            $usuarioDB = getenv('DB_USER');
            $contraseñaDB = getenv('DB_PASS');

            $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuarioDB, $contraseñaDB);
            $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            $ZonaHoraria = getenv('ZonaHoraria');
            date_default_timezone_set($ZonaHoraria);
            $fechahora = date('Ymd H:i:s');
            $usuarioId = (!empty($_POST['user'])) ? $_POST['user'] : NULL;
            $password = (!empty($_POST['confirmed_password'])) ? $_POST['confirmed_password'] : NULL;

            $data = $_POST['marcar'];
            $partes = explode("_", $data); 
            $IdTarja = $partes[0];
            $TipoRemision = isset($partes[1]) ? $partes[1] : '';
            $Almacen = isset($partes[2]) ? $partes[2] : ''; 

            $stmt = $Conexion->prepare("SELECT Contrasenia FROM t_usuario WHERE IdUsuario = ?");
            $stmt->execute([$usuarioId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$user || !password_verify($password, $user['Contrasenia'])) {
                mostrarAlerta('error', 'Error', 'La contraseña no es válida');
                return;
            }

            try 
            {
                if($TipoRemision==1)
                {
                    $stmt = $Conexion->prepare("SELECT distinct(IdRemision) as IdRemision FROM t_ingreso where IdTarja=? and Almacen=?");
                    $stmt->execute([$IdTarja,$Almacen]);
                    $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if (empty($Remisiones)) 
                    {
                        mostrarAlerta('error', 'Error', 'No se encontraron datos de la tarja especificada');
                    }

                    $Conexion->beginTransaction();

                    $stmtDelete = $Conexion->prepare("DELETE FROM t_ingreso where IdTarja=? and IdRemision=? and Almacen=?");
                    $stmtDeleteFoto = $Conexion->prepare("DELETE FROM t_fotografias_Encabezado WHERE IdTarja=? and Almacen=?");
                    $stmtDeleteFotoDet = $Conexion->prepare("DELETE FROM t_fotografias_Detalle WHERE idfotografiaref in(Select IdFotografias from t_fotografias_Encabezado where IdTarja=? and Almacen=?)");
                    
                    foreach($Remisiones as $index => $Remision) {
                        $IdRemision = $Remision->IdRemision;

                        $result = $stmtDelete->execute([$IdTarja,$IdRemision,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el Ingreso");
                        }

                         $result = $stmtDeleteFoto->execute([$IdTarja,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el FotoEnc");
                        }
                         $result = $stmtDeleteFotoDet->execute([$IdTarja,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el FotoDet");
                        }

                        $stmtUpdateRemision = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 1 
                        WHERE IdRemisionEncabezado = ? ");

                        $result = $stmtUpdateRemision->execute([$IdRemision]);
                        
                        if (!$result) {
                            throw new Exception("Error al actualizar remisión");
                        }
                    }

                    $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                                VALUES (?, ?, ?, ?, ?)";
                    $stmt = $Conexion->prepare($consulta);
                    $stmt->execute([
                        't_ingreso', 
                        'CancelarRemision'.$IdTarja, 
                        $fechahora, 
                        "Cancelar remisión", 
                        $usuarioId
                    ]);

                    $Conexion->commit();
                    mostrarAlerta('success', 'Éxito', 'Remisión cancelada correctamente');
                }

                elseif($TipoRemision==2)
                {
                    $stmt = $Conexion->prepare("SELECT distinct(IdRemision) as IdRemision FROM t_Salida where IdTarja=? and Almacen=?");
                    $stmt->execute([$IdTarja,$Almacen]);
                    $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if (empty($Remisiones)) {
                        mostrarAlerta('error', 'Error', 'No se encontraron datos de la tarja especificada');
                    }

                    $Conexion->beginTransaction();

                    $stmtDelete = $Conexion->prepare("DELETE FROM t_Salida where IdTarja=? and IdRemision=? and Almacen=?");
                    $stmtDeleteFoto = $Conexion->prepare("DELETE FROM t_fotografias_Encabezado WHERE IdTarja=? and Almacen=?");
                    $stmtDeleteFotoDet = $Conexion->prepare("DELETE FROM t_fotografias_Detalle WHERE idfotografiaref in(Select IdFotografias from t_fotografias_Encabezado where IdTarja=? and Almacen=?)");
                    $stmtDeleteFotoRemLinea = $Conexion->prepare("DELETE FROM t_remision_linea WHERE IdRemisionEncabezadoRef=?");
                    
                    foreach($Remisiones as $index => $Remision) {
                        $IdRemision = $Remision->IdRemision;

                        $result = $stmtDelete->execute([$IdTarja,$IdRemision,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el Salida");
                        }
                        
                         $result = $stmtDeleteFoto->execute([$IdTarja,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el FotoEnc");
                        }
                         $result = $stmtDeleteFotoDet->execute([$IdTarja,$Almacen]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el FotoDet");
                        }

                         $result = $stmtDeleteFotoRemLinea->execute([$IdRemision]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el FotoDet");
                        }




                    $stmtDelete2 = $Conexion->prepare("DELETE FROM t_pasoSalida where IdRemision=?");

                        $result = $stmtDelete2->execute([$IdRemision]);

                        if (!$result) {
                            throw new Exception("Error al eliminar el Salida");
                        }

                        $stmtUpdateRemision = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 1 
                        WHERE IdRemisionEncabezado = ? and Almacen=?");

                        $result = $stmtUpdateRemision->execute([$IdRemision,$Almacen]);
                        
                        if (!$result) {
                            throw new Exception("Error al actualizar remisión");
                        }
                    }

                    $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                                VALUES (?, ?, ?, ?, ?)";
                    $stmt = $Conexion->prepare($consulta);
                    $stmt->execute([
                        't_Salida', 
                        'CancelarRemision'.$IdTarja, 
                        $fechahora, 
                        "Cancelar remisión", 
                        $usuarioId
                    ]);

                    $Conexion->commit();

                    mostrarAlerta('success', 'Éxito', 'Remisión cancelada correctamente');
                }

            }catch (PDOException $e) 
                {
                    $Conexion->rollBack();
                    mostrarAlerta('error', 'Error', 'Ocurrió un error al cancelar: '.$e->getMessage());
                }
        } catch (PDOException $e) {
              echo "Error de conexión: " . $e->getMessage();
      } finally {
          $conexion = null;
      }
    }


    function mostrarAlerta($icon, $title, $text) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script language='JavaScript'>
            document.addEventListener('DOMContentLoaded',function(){
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text'
                }).then(function() {
                    window.location = 'CancelarRemision.php';
                });
            });
        </script>";
    }
?>