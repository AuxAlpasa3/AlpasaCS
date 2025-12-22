<?php
Include_once "../templates/head.php";
$fecha = date('Ymd');
$fechahora = date('Ymd H:i:s');
$usuario = (!empty($_POST['user'])) ? $_POST['user'] : NULL;
$IdRemisionEncabezado = (!empty($_POST['id'])) ? $_POST['id'] : NULL;
$IdRemision = (!empty($_POST['IdRemision'])) ? $_POST['IdRemision'] : NULL;
$Almacen = (!empty($_POST['Almacen'])) ? $_POST['Almacen'] : NULL;
?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php 
     Include_once  "../templates/nav.php";
     Include_once  "../templates/aside.php";
     ?>    
<style>
  #DarSalida:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #cccccc !important;
    border-color: #cccccc !important;
  }
  .card-disabled {
    opacity: 0.6;
    pointer-events: none;
  }
  .search-container {
    margin-bottom: 15px;
  }
</style>
    <div class="content-wrapper">
      <section class="content mt-4">
        <div class="container-fluid">
        </br>
          <div class="row">
            <div class="col-12">
              <div class="card">
                 <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h1 class="card-title">Remisión para Salida: <b><?php echo "REM ".$IdRemision; ?></b> </h1>
                </div>
                <div class="card-body"> 
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                        <div class="row">
                          <div class="col-12">
                            <?php
                             $sentSalidas = $Conexion->prepare("SELECT t2.IdLinea,t5.NombreCliente,t3.MaterialNo, Concat(t3.Material,' ',t3.Shape) as Articulo, t2.Piezas, t1.IdRemision, t1.IdRemisionEncabezado,
                              t2.Piezas-sum(isnull(t4.Piezas,0)) as Faltan,sum(isnull(t4.Piezas,0)) as totales
                              FROM t_remision_encabezado as t1 
                              INNER JOIN t_remision_linea as t2 on t1.IdRemision=t2.IdRemision 
                              INNER JOIN t_articulo as t3 on t2.IdArticulo =t3.IdArticulo 
                              LEFT JOIN t_pasoSalida as t4 on t1.IdRemisionEncabezado=t4.IdRemision and t2.IdLinea=t4.IdLinea
                              INNER JOIN t_cliente as t5 on t1.Cliente=t5.IdCliente 
                              INNER JOIN t_usuario_almacen as t6 on t1.Almacen=t6.IdAlmacen 
                              where t1.TipoRemision=2 and t1.IdRemision=? and t6.IdUsuario=? and  t1.IdRemisionEncabezado=?
                              group by t2.IdLinea,t3.MaterialNo,t3.Material,t3.Shape,t2.Piezas,t1.IdRemision,T5.NombreCliente,t1.IdRemisionEncabezado");
                             $sentSalidas->execute([$IdRemision,$IdUsuario,$IdRemisionEncabezado]);
                             $Salidas = $sentSalidas->fetchAll(PDO::FETCH_OBJ);
                            ?>
                            <div class="row">
                              <div class="col-12">
                                <section class="pt-2">
                                  <div class="table-responsive">
                                    <form id="formLote" action="ProcesoSalida/Salida.php" method="POST" enctype="multipart/form-data">
                                      <input type="text" name="user" value="<?php echo $IdUsuario;?>" hidden>
                                      <input type="text" name="IdRemisionEncabezado" value="<?php echo $IdRemisionEncabezado;?>" hidden>
                                      <input type="text" name="IdRemision" value="<?php echo $IdRemision;?>" hidden>
                                      <input type="text" name="Almacen" value="<?php echo $Almacen;?>" hidden>

                                    <button type="submit" class="btn btn-danger" style=" background-color: #d94f00; !important;" id="DarSalida">Guardar</button>
                                      <table class="table table-bordered  table-striped" id="dataTable" > 
                                      <thead>
                                        <tr>
                                         <th width="auto" style="color:black; text-align: center;"></th>
                                         <th width="auto" style="color:black; text-align: center;">#</th>
                                         <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                         <th width="auto" style="color:black; text-align: center;">MaterialNo.</th>
                                         <th width="auto" style="color:black; text-align: center;">Articulo</th>
                                         <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                         <th width="auto" style="color:black; text-align: center;">Faltan</th>
                                         <th width="auto" style="color:black; text-align: center;">Total</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php
                                           foreach($Salidas as $Salida){
                                            $IdLinea=$Salida->IdLinea;
                                            $IdRemision=$Salida->IdRemision;
                                            $IdRemisionEncabezado=$Salida->IdRemisionEncabezado;
                                            ?>
                                        <tr>
                                          <td width="auto" style="text-align: center;">

                                            <input type="radio" name="Elegir" value="<?php echo $Salida->IdLinea;?>" 
                                            onclick="multipleActions(this.value,<?php echo $IdRemisionEncabezado; ?>,'<?php echo $Salida->MaterialNo;?>',<?php echo $Salida->Faltan; ?>,<?php echo $Almacen; ?>)" data-materialno="<?php echo $Salida->MaterialNo; ?>">
                                          </td>
                                          <td width="auto" style="text-align: center; ">
                                            <b><?php echo $Salida->IdLinea;?></b>
                                          </td>
                                          <td width="auto" style="text-align: center;">
                                            <b><?php echo $Salida->NombreCliente;?></b>    
                                          </td>
                                          <td width="auto" style="text-align: center;">
                                            <b><?php echo $Salida->MaterialNo;?></b>    
                                          </td>
                                          <td width="auto" style="text-align: center;">
                                            <b><?php echo $Salida->Articulo;?></b>
                                          </td>
                                          <td width="auto" style="text-align: center;">
                                            <b><?php echo $Salida->Piezas;?></b>
                                          </td>
                                          <td width="auto" style="text-align: center; " >
                                            <b <?php
                                                  if($Salida->Faltan>0)
                                                    echo "style='color: red;'";
                                                ?>>
                                              <?php echo $Salida->Faltan;?>    
                                            </b>
                                          </td>
                                          <td width="auto" style="text-align: center;">
                                            <b <?php
                                                  if($Salida->totales==$Salida->Piezas)
                                                    echo "style='color: Green;'";?>>
                                                <?php echo $Salida->totales;?>
                                            </b>
                                          </td>
                                        </tr>
                                        <?php  
                                            }
                                        ?>
                                      </tbody>
                                      </table>
                                    </form>
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

      <script type="text/javascript">
          document.getElementById('formLote').addEventListener('submit', function(e) {
              e.preventDefault();
              
              Swal.fire({
                  title: 'Procesando',
                  html: 'Generando la salida, por favor espere...',
                  allowOutsideClick: false,
                  didOpen: () => {
                      Swal.showLoading();
                  }
              });
              
              const formData = new FormData(this);
              
              fetch('ProcesoSalida/Salida.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => response.json())
              .then(data => {
                  Swal.close();
                  
                  if(data.success) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Éxito',
                          text: data.message,
                          confirmButtonColor: '#d94f00',
                          confirmButtonText: 'Aceptar'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = 'SalidasPendientes.php';
                          }
                      });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: data.message,
                          confirmButtonColor: '#d94f00',
                          confirmButtonText: 'Aceptar'
                      });
                  }
              })
              .catch(error => {
                  Swal.close();
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Ocurrió un error al procesar la solicitud',
                      confirmButtonColor: '#d94f00',
                      confirmButtonText: 'Aceptar'
                  });
                  console.error('Error:', error);
              });
          });

          function multipleActions(idLote,IdRemision,MaterialNo,faltan,IdAlmacen) {
            mostrarSeleccionados(idLote,IdRemision,MaterialNo,IdAlmacen);
            mostrarLote(idLote,IdRemision,MaterialNo,faltan,IdAlmacen);
            verificarFaltan(); 
          }

          function mostrarLote(idLote, IdRemision, MaterialNo, faltan,IdAlmacen) {
            fetch('obtenerlote.php?id=' + idLote + '&idRemision=' + IdRemision + '&MaterialNo=' + MaterialNo +'&IdAlmacen='+IdAlmacen)
            .then(response => response.json())
            .then(data => {
                const tablaBody = document.querySelector('#tablaLote tbody');
                tablaBody.innerHTML = '';

                data.forEach(registro => {
                    const row = document.createElement('tr');
                    
                    let codBarrasFormateado;
                    if (registro.EsArmado == 1) {
                        codBarrasFormateado = `ARM-${String(registro.CodBarras).padStart(6, '0')}`;
                    } else {
                       codBarrasFormateado = `${String(registro.NumRecinto)}-${String(registro.CodBarras).padStart(6, '0')}`;
                    }
                    
                    const maxPermitido = Math.min(registro.Piezas, faltan);
                    
                    row.innerHTML = `
                        <td width="auto" style="text-align: center;">
                            <input type="radio" class="select-radio" name="loteSeleccionado" value="${idLote}" onchange="toggleInput(this)">
                        </td>
                        <td style="text-align: center;" hidden>${registro.CodBarras}</td> 
                        <td style="text-align: center;">${codBarrasFormateado}</td> 
                        <td style="text-align: center;">${registro.NombreCliente}</td>
                        <td style="text-align: center;">${registro.FechaProduccion}</td>
                        <td style="text-align: center;">${registro.Periodicidad}</td>
                        <td style="text-align: center;">${registro.MaterialNo}</td>
                        <td style="text-align: center;">${registro.Articulo}</td>
                        <td style="text-align: center;">${registro.Piezas}</td>
                        <td style="text-align: center;" hidden="${!registro.EsArmado}">${registro.EsArmado}</td>
                        <td style="text-align: center;" hidden="${!registro.EstadoMaterial}">${registro.EstadoMaterial}</td>
                        <td style="text-align: center;">
                            <input class="Form-Control" style="text-align: center;" type="number" name="piezass" value="" id="piezass" disabled max="${maxPermitido}" min="1" oninput="validarPiezas(this)">
                        </td>
                    `;
                    tablaBody.appendChild(row);
                });
                          const cardInventario = document.getElementById('cardInventario');
                          const btnAñadir = document.getElementById('btnAñadir');

                          if (faltan > 0) {
                              cardInventario.classList.remove('card-disabled'); 
                              btnAñadir.disabled = false;
                          } else {
                              cardInventario.classList.add('card-disabled');
                              btnAñadir.disabled = true; 
                          }
                      });
          }
          
          function limpiarSeleccion() {
            const cardInventario = document.getElementById('cardInventario');
            const btnAñadir = document.getElementById('btnAñadir');

            cardInventario.classList.add('card-disabled'); 
            btnAñadir.disabled = true; 
          }

          document.querySelectorAll('input[name="Elegir"]').forEach(radio => {
            radio.addEventListener('change', () => {
              if (!radio.checked) {
                limpiarSeleccion();
              }
            });
          });

          function mostrarSeleccionados(idLote, IdRemision, MaterialNo,IdAlmacen) {
            fetch('obtenerSeleccionados.php?id=' + idLote + '&idRemision=' + IdRemision + '&MaterialNo=' +  MaterialNo +'&IdAlmacen='+IdAlmacen)
                .then(response => response.json())
                .then(data => {
                    const tablaBody = document.querySelector('#infolote tbody');
                    tablaBody.innerHTML = '';
                    
              const tieneRegistros = data.length > 0;
              let codBarrasUnico = null;
              let multiplesCodigos = false;

              if (tieneRegistros) {
                  codBarrasUnico = data[0].CodBarras;
                  multiplesCodigos = data.some(reg => reg.CodBarras !== codBarrasUnico);
              }
              const cardInventario = document.getElementById('cardInventario');
              const btnAñadir = document.getElementById('btnAñadir');
              
              if (tieneRegistros && multiplesCodigos) {
                  cardInventario.classList.add('card-disabled');
                  btnAñadir.disabled = true;
                  document.querySelectorAll('#tablaLote input[type="radio"]').forEach(radio => {
                      radio.disabled = true;
                  });
              } else {
                  cardInventario.classList.remove('card-disabled');
                  btnAñadir.disabled = false;
                  document.querySelectorAll('#tablaLote input[type="radio"]').forEach(radio => {
                      radio.disabled = false;
                  });
              }

              data.forEach(elegidas => {
                  const codBarrasFormateado = `${String(elegidas.NumRecinto)}-${String(elegidas.CodBarras).padStart(6, '0')}`;
                  const row = document.createElement('tr');
                  row.innerHTML = `
                      <td style="text-align: center;">${codBarrasFormateado}</td> 
                      <td width="auto" style="text-align: center;">${elegidas.NombreCliente}</td>
                      <td width="auto" style="text-align: center;">${elegidas.MaterialNo}</td>
                      <td width="auto" style="text-align: center;">${elegidas.Articulo}</td>
                      <td style="text-align: center;">${elegidas.Piezas}</td>
                      <td width="auto" style="text-align: center;">
                          <button type="button" class="btn btn-danger" id="btnEliminar" onclick="EliminarRegistro(${idLote},${IdRemision},${elegidas.CodBarras})">Eliminar</button>
                      </td>`;
                  tablaBody.appendChild(row);
              });
            });
          }
          
          function validarPiezas(input) {
            const maxPiezas = parseInt(input.getAttribute('max'));
            const valorIngresado = parseInt(input.value) || 0;

            if (valorIngresado > maxPiezas) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `No puedes ingresar más de ${maxPiezas} piezas.`,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#d94f00',
              }).then(() => {
                input.value = maxPiezas;
              });
            } else if (valorIngresado < 1) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes ingresar al menos 1 pieza.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#d94f00',
              }).then(() => {
                input.value = 1;
              });
            }
          }

          function toggleInput(radio) {
            var row = radio.closest('tr');
            var inputNumber = row.querySelector('input[type="number"]');
            document.querySelectorAll('input[type="number"][name="piezass"]').forEach(input => {
              input.disabled = true;
              input.value = '';
            });
            inputNumber.disabled = !radio.checked;
            if (radio.checked) {
              inputNumber.value = 0;
            }
          }

          function añadirRegistros(IdRemision) {
            const radio = document.querySelector('input[name="loteSeleccionado"]:checked');
              
              if (!radio) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Por favor, selecciona un registro.',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#d94f00'
                  });
                  return;
              }

              const fila = radio.parentElement.parentElement;
              const CodBarras = fila.cells[1].textContent;
              const MaterialNo = fila.cells[6].textContent;
              const EsArmado = fila.cells[9].textContent;
              const EstadoMaterial = fila.cells[10].textContent; 
              const piece = parseInt(fila.querySelector('#piezass').value) || 0;
              const Estatus = 1;
              const maxPermitido = parseInt(fila.querySelector('#piezass').getAttribute('max'));

              const registrosSeleccionados = document.querySelectorAll('#infolote tbody tr td:first-child');
              let codBarrasExistente = null;
              
              if (registrosSeleccionados.length > 0) {
                  codBarrasExistente = registrosSeleccionados[0].textContent.split('-').pop();
                  const nuevoCodBarras = `ALP-${String(CodBarras).padStart(6, '0')}`.split('-').pop();
                  
                  if (codBarrasExistente !== nuevoCodBarras) {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Solo puedes agregar registros con el mismo código de barras.',
                          confirmButtonText: 'Aceptar',
                          confirmButtonColor: '#d94f00'
                      });
                      return;
                  }
              }
              if (piece <= 0) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Por favor, ingresa un número de piezas mayor a 0.',
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#d94f00'
                  });
                  return;
              }

              if (piece > maxPermitido) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: `No puedes ingresar más de ${maxPermitido} piezas.`,
                      confirmButtonText: 'Aceptar',
                      confirmButtonColor: '#d94f00'
                  });
                  return;
              }

               if (EstadoMaterial == 8) {
                mostrarConfirmacionObsoleto().then((result) => {
                    if (result.isConfirmed) {
                        enviarRegistro(radio.value, IdRemision, CodBarras, MaterialNo, piece, EsArmado, Estatus, MaterialNo);
                    }
                });
            } else {
                enviarRegistro(radio.value, IdRemision, CodBarras, MaterialNo, piece, EsArmado, Estatus, MaterialNo);
            }
        }

         function mostrarConfirmacionObsoleto() {
            return Swal.fire({
                title: '¡Advertencia!',
                html: `
                    <div class="alert alert-warning" style="background-color: #d94f00; color:white;">
                        <i class="fas fa-exclamation-triangle"></i> Está intentando añadir un registro con estado OBSOLETO. Favor de Ingresar la contraseña de un Gerente o Supervisor.
                    </div>
                    <p>¿Desea continuar con esta acción?</p>
                    <div class="form-group">
                        <label for="password">Contraseña de autorización:</label>
                        <input type="password" class="form-control" id="passwordAuth" placeholder="Ingrese la contraseña">
                    </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d94f00',
                preConfirm: () => {
                    const password = document.getElementById('passwordAuth').value;
                    if (!password) {
                        Swal.showValidationMessage('La contraseña es requerida');
                        return false;
                    }
                    
                    return validarPasswordObsoleto(password).then(esValida => {
                        if (!esValida) {
                            Swal.showValidationMessage('Contraseña incorrecta');
                            return false;
                        }
                        return true;
                    });
                }
            });
        }

        function validarPasswordObsoleto(password) {
            return fetch('validarPasswordObsoleto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    password: password,
                    accion: 'autorizar_obsoleto'
                })
            })
            .then(response => response.json())
            .then(data => {
                return data.esValida;
            })
            .catch(error => {
                console.error('Error al validar contraseña:', error);
                return false;
            });
        }

        function enviarRegistro(id, IdRemision, CodBarras, MaterialNo, piece, EsArmado, Estatus, idRemision, materialNo) {
            const registro = {
                id: id,
                IdRemision: IdRemision,
                CodBarras: CodBarras,
                MaterialNo: MaterialNo,
                piece: piece,
                EsArmado: EsArmado,
                Estatus: Estatus
            };

            fetch('ProcesoSalida/AgregarRegistroBD.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify([registro])
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const idLinea = document.querySelector('input[name="Elegir"]:checked').value;
                    mostrarSeleccionados(idLinea, IdRemision, MaterialNo);
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Se ha agregado Correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#d94f00'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Favor de Validar con el Administrador",
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#d94f00'
                    });
                }
            })
            .catch(error => {
                console.error('Error al enviar los datos:', error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un problema al añadir el registro.",
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d94f00'
                });
            });
        }

          function EliminarRegistro(idLote,IdRemision,CodBarras) {
              Swal.fire({
              title: '¿Estás seguro?',
              text: `¿Quieres eliminar el registro con CodBarras:  RHI-ALP-${CodBarras}?`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar',
               confirmButtonColor: '#d94f00',
              reverseButtons: true
                }).then((result) => {
              if (result.isConfirmed) {
                  var xhr = new XMLHttpRequest();
                  xhr.open("POST", "ProcesoSalida/EliminarRegistroBD.php", true);
                  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                  xhr.onreadystatechange = function() {
                      if (xhr.readyState == 4 && xhr.status == 200) {
                          Swal.fire({
                      title: 'Eliminado',
                      text: `El registro ha sido eliminado.'`,
                      icon: 'success',
                       confirmButtonColor: '#d94f00'
                }
                          ).then(() => {
                              location.reload(); 
                          });
                      }
                  };
                  xhr.send("idLote=" + idLote + "&IdRemision=" + IdRemision+ "&CodBarras=" +CodBarras);
              }
              });
          }

          function verificarFaltan() {
            let todosCero = true;
            document.querySelectorAll('tbody tr td:nth-child(7) b').forEach(cell => {
              const faltan = parseInt(cell.textContent);
              if (faltan > 0) {
                todosCero = false;
              }
            });
            
            document.getElementById('DarSalida').disabled = !todosCero;
          }

          function searchTable() {
              const input = document.getElementById('searchInput');
              const filter = input.value.toUpperCase();
              const table = document.getElementById('tablaLote');
              const tr = table.getElementsByTagName('tr');

              for (let i = 1; i < tr.length; i++) {
                  let found = false;
                  const td = tr[i].getElementsByTagName('td');
                  
                  for (let j = 0; j < td.length; j++) {
                      if (td[j]) {
                          const txtValue = td[j].textContent || td[j].innerText;
                          if (txtValue.toUpperCase().indexOf(filter) > -1) {
                              found = true;
                              break;
                          }
                      }
                  }
                  
                  tr[i].style.display = found ? '' : 'none';
              }
          }

          function clearSearch() {
              document.getElementById('searchInput').value = '';
              searchTable();
          }

          document.addEventListener('DOMContentLoaded', function() {
            verificarFaltan();
            
            document.getElementById('searchInput').addEventListener('keyup', function() {
                searchTable();
            });
          });

      </script>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6"> 
                      <div class="card" style=" display: flex;" id="cardInventario">
                         <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                          <h1 class="card-title">Total de Inventario</h1>
                        </div>
                        <div class="card-body">
                        <div class="col-12">
                            <div class="row">
                              <div class="col-12">
                                <section class="pt-2">
                                  <div class="search-container">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar en inventario...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="searchTable()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="table-responsive">
                                    <table class="table table-bordered  table-striped" id="tablaLote" name="tablaLote"> 
                                      <thead>
                                        <tr>
                                          <th  style="color:black; text-align: center;"></th>
                                          <th  style="color:black; text-align: center;">CodBarras</th>
                                          <th  style="color:black; text-align: center;">Cliente</th>
                                          <th  style="color:black; text-align: center;">Fecha Produccion</th>
                                          <th  style="color:black; text-align: center;">Periodicidad</th>
                                          <th  style="color:black; text-align: center;">MaterialNo</th>
                                          <th  style="color:black; text-align: center;">Articulo.</th>
                                          <th  style="color:black; text-align: center;">Piezas</th>
                                          <th  style="color:black; text-align: center;">Piezas</th>
                                        </tr>
                                      </thead>
                                      <tbody id="sourceTable">
                                      </tbody>
                                    </table>
                                    <button type="button"  class="btn btn-danger"  style=" background-color: #d94f00; !important;" id="btnAñadir" onclick="añadirRegistros(<?php echo $IdRemisionEncabezado;?>)">Añadir</button>
                                  </div>
                                </section>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!--TABLA DE REGISTROS SELECCIONADOS--->
                    <div class="col-6">
                      <div class="card" style=" display: flex;">
                         <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                          <h1 class="card-title">Seleccionados:</h1>
                        </div>
                        <div class="card-body">
                          <div class="col-12">
                            <div class="row">
                              <div class="col-12">
                                <section class="pt-2">
                                  <div class="table-responsive">
                                    <table class="table table-bordered  table-striped" id="infolote" name="infolote">
                                      <thead>
                                        <tr>
                                         <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                         <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                         <th width="auto" style="color:black; text-align: center;">MaterialNo</th>
                                         <th width="auto" style="color:black; text-align: center;">Articulo.</th>
                                         <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                         <th width="auto" style="color:black; text-align: center;"></th>
                                        </tr>
                                      </thead>
                                      <tbody id=infolote>
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
          </section>
        </div>
              <?php include_once '../templates/footer.php' ?>
      <aside class="control-sidebar"></aside>
    </div>
  </body>
</html>