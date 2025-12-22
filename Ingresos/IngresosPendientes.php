<?php
    Include_once "../templates/head.php";
    require_once '../vendor/autoload.php';
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
    require "../vendor/phpqrcode/qrlib.php";

    use PhpOffice\PhpWord\PhpWord;
    use PhpOffice\PhpWord\IOFactory;
    use PhpOffice\PhpWord\Style\Font;
    
    function obtenerColorPorEstado($idEstado) {
        $colores = [
            1 => '#28a745', 2 => '#dc3545', 3 => '#fd7e14', 4 => '#ffc107', 5 => '#17a2b8', 6 => '#6f42c1',7 => '#e83e8c', 8 => '#20c997', 9 => '#6610f2', 10 => '#d63384', 11 => '#6c757d', 12 => '#0dcaf0', 13 => '#ff6b6b', 14 => '#4ecdc4', 15 => '#ff9f43', 16 => '#a55eea', 17 => '#45aaf2', 18 => '#fc5c65', 19 => '#fd9644', 20 => '#786fa6', 
        ];
        
        if (isset($colores[$idEstado])) {
            return $colores[$idEstado];
        }
    
        $hash = md5($idEstado);
        return '#' . substr($hash, 0, 6);
    }
?>
<style>
    .highlighted td {
        background: #c3c3c3;
    }
    .badge-estado {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 110%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        margin: 2px;
        color: white;
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
                  <h1 class="card-title">INGRESOS PENDIENTES</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                      <div class="row">
                        <div class="col-12">
                           <form name="EnviarSAP" id="EnviarSAP" action="" method="POST" enctype="multipart/form-data">
                           <button type="submit" name="Mov" id="Mov" class="btn btn-success" value="EnviarSAP" disabled>Enviar a SAP</button>
                                <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                <input type="hidden" id="IdAlmacen" name="IdAlmacen" value="">
                            <?php
                             $sentIngresos = $Conexion->query("SELECT t1.IdTarja AS IdTarja,  t1.IdTarja as IdTarjaNum,  t1.CodBarras AS CodBarras,  t1.CodBarras as CodBarrasNum,  CONVERT(DATE, t1.FechaIngreso) as FechaIngreso, CASE WHEN t1.FechaProduccion IS NULL THEN 'N/A' WHEN ISDATE(t1.FechaProduccion) = 1 THEN CONVERT(VARCHAR, CAST(t1.FechaProduccion AS DATE)) ELSE 'Fecha Inválida' END AS FechaProduccion,  t1.IdArticulo,  t2.MaterialNo,   TRIM(CONCAT(t2.Material, ' ', t2.Shape)) as MaterialShape,  t1.Piezas,  t1.NumPedido,  t1.NetWeight,  t1.GrossWeight,  t1.IdUbicacion,  t3.Ubicacion, STUFF(( SELECT ', ' + tem.EstadoMaterial FROM STRING_SPLIT(t1.EstadoMercancia, ',') estado INNER JOIN t_estadoMaterial tem ON CAST(estado.value AS INT) = tem.IdEstadoMaterial FOR XML PATH('') ), 1, 2, '') as EstadoMercancia,t1.EstadoMercancia as EstadosIds,t1.Origen, t1.Cliente, t4.NombreCliente, t7.IdRemision, t1.IdLinea, t1.Transportista, TRIM(t1.Placas) as Placas, t1.Chofer, t1.Checador, t1.Supervisor,  (CASE WHEN t1.Comentarios IS NULL THEN 'SIN COMENTARIOS' ELSE t1.Comentarios END) as Comentarios,  (CASE WHEN t1.PaisOrigen IS NULL THEN 'Sin Pais Origen Registrado' ELSE t1.PaisOrigen END) as PaisOrigen, t1.NoTarima, t8.NumRecinto, t1.Almacen FROM t_ingreso as t1  
                                INNER JOIN t_articulo as t2 ON t1.IdArticulo = t2.IdArticulo 
                                LEFT JOIN t_ubicacion as t3 ON t1.IdUbicacion = t3.IdUbicacion 
                                INNER JOIN t_cliente as t4 ON t1.Cliente = t4.IdCliente  
                                INNER JOIN t_usuario_almacen as t6 ON t1.Almacen = t6.IdAlmacen  
                                INNER JOIN t_remision_encabezado as t7 ON t1.IdRemision = t7.IdRemisionEncabezado 
                                INNER JOIN t_almacen as t8 ON t1.Almacen = t8.IdAlmacen 
                                WHERE t1.ESTATUS IN (0,1,2,3) AND t6.IdUsuario = $IdUsuario  
                                ORDER BY t1.IdRemision, t1.IdLinea;");
                                $Ingresos = $sentIngresos->fetchAll(PDO::FETCH_OBJ);
                               
                            ?> 
                          <div class="row">
                            <div class="col-12"> 
                                  <section class="pt-2">
                                    <div class="table-responsive">
                                      <table class="table table-bordered table-striped" id="tablaconB" > 
                                        <thead>
                                          <tr>
                                           <th width="auto" style="color:black; text-align: center;">   <input type="checkbox"  onClick="toggle(this)"></th>
                                           <th width="auto" style="color:black; text-align: center;">Tarja</th>
                                           <th width="auto" style="color:black; text-align: center;">CodBarras</th>
                                           <th width="auto" style="color:black; text-align: center;">Fecha Ingreso</th>
                                           <th width="auto" style="color:black; text-align: center;">Fecha Produccion</th>
                                           <th width="auto" style="color:black; text-align: center;">Material No.</th>
                                           <th width="auto" style="color:black; text-align: center;">Material/Shape</th>
                                           <th width="auto" style="color:black; text-align: center;">Piezas</th>
                                           <th width="auto" style="color:black; text-align: center;">Pedido</th>
                                           <th width="auto" style="color:black; text-align: center;">Net Weight</th>
                                           <th width="auto" style="color:black; text-align: center;">Gross Weight</th>
                                           <th width="auto" style="color:black; text-align: center;">Ubicacion</th>
                                           <th width="auto" style="color:black; text-align: center;">Estado Material</th>
                                           <th width="auto" style="color:black; text-align: center;">Destino</th>
                                           <th width="auto" style="color:black; text-align: center;">Origen</th>
                                           <th width="auto" style="color:black; text-align: center;">Cliente</th>
                                           <th width="auto" style="color:black; text-align: center;">IdRemision</th>
                                           <th width="auto" style="color:black; text-align: center;">Comentarios</th>
                                           <th width="auto" style="color:black; text-align: center;"></th>
                                           <th width="auto" style="color:black; text-align: center;"></th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <?php
                                             foreach($Ingresos as $Ingreso){
                                              $CodBarras=$Ingreso->CodBarras;
                                              
                                               $estadosBadges = '';
                                              if (!empty($Ingreso->EstadosIds)) {
                                                  $idsEstados = explode(',', $Ingreso->EstadosIds);
                                                  $nombresEstados = explode(', ', $Ingreso->EstadoMercancia);
                                                  
                                                  for ($i = 0; $i < count($idsEstados); $i++) {
                                                      $idEstado = trim($idsEstados[$i]);
                                                      $nombreEstado = trim($nombresEstados[$i]);
                                                      
                                                      if (!empty($idEstado) && !empty($nombreEstado)) {
                                                          $color = obtenerColorPorEstado($idEstado);
                                                          $estadosBadges .= '<span class="badge-estado" style="background-color: ' . $color . ';">' . 
                                                                           htmlspecialchars($nombreEstado) . '</span> ';
                                                      }
                                                  }
                                              }
                                              ?>
                                          <tr>
                                            <td width="auto" style="text-align: center;">
                                                <input type="checkbox" name="marcar[]" value="<?php echo $Ingreso->CodBarrasNum; ?>" data-almacen="<?php echo $Ingreso->Almacen; ?>">
                                            </td>
                                      </form>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo 'ALP'.$Ingreso->NumRecinto.'-ING-'.sprintf("%04d", $Ingreso->IdTarja);?>
                                            </td> 
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NumRecinto."-". sprintf("%06d", $CodBarras=$Ingreso->CodBarras);?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->FechaIngreso;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->FechaProduccion;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->MaterialNo;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->MaterialShape;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Piezas;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NumPedido;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo number_format($Ingreso->NetWeight, 0, '.', ',');?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo number_format($Ingreso->GrossWeight, 0, '.', ',');?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Ubicacion;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $estadosBadges; ?>
                                            </td>
                                             <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->PaisOrigen;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->Origen;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                              <?php echo $Ingreso->NombreCliente;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                            <?php echo $Ingreso->IdRemision;?>
                                            </td>
                                            <td width="auto" style="text-align: center;">
                                            <?php echo $Ingreso->Comentarios;?>
                                            </td>
                                             <td width="auto" style="text-align: center;">
                                                <button type="button" 
                                                        class="btn-editar btn btn-warning" 
                                                        data-id="<?php echo $CodBarras=$Ingreso->CodBarras;?>">
                                                    <i class="fa fa-pen"></i>
                                                </button>
                                            </td>
                                            <td>
                                               <form name="ImprimirQR" id="ImprimirQR" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" id="CodBarrasNum" name="CodBarrasNum" value="<?php echo $CodBarras=$Ingreso->CodBarras; ?>">
                                                    <input type="hidden" id="Almacen" name="Almacen" value="<?php echo $Ingreso->Almacen; ?>">
                                                    <input type="hidden" id="Mov" name="Mov" value="ImprimirQR">
                                                    <button type="button" name="Mov" id="Mov" class="btn btn-sm" style="color: white; background-color: #d94f00;" onclick="seleccionarImpresora(this)" value="ImprimirQR">Imprimir QR</button>
                                                </form>
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
      </section>
    </div>
    <?php include_once '../templates/footer.php' ?>
    <aside class="control-sidebar">
    </aside>
  </div>
</body>
</html>

<div id="modal-container"></div>

<script type="text/javascript">
    function verificarCheckboxes() {
        const checkboxes = document.getElementsByName('marcar[]');
        const btnEnviarSAP = document.getElementById('Mov');
        const idAlmacenInput = document.getElementById('IdAlmacen');

        const checkboxesMarcados = Array.from(checkboxes).filter(checkbox => checkbox.checked);
        const algunoMarcado = checkboxesMarcados.length > 0;
        
        btnEnviarSAP.disabled = !algunoMarcado;

        if (algunoMarcado) {
            const primerCheckboxMarcado = checkboxesMarcados[0];
            const idAlmacen = primerCheckboxMarcado.getAttribute('data-almacen');
            idAlmacenInput.value = idAlmacen;
        } else {
            idAlmacenInput.value = '';
        }
    }

    function toggle(source) {
        const checkboxes = document.getElementsByName('marcar[]');
        const idAlmacenInput = document.getElementById('IdAlmacen');
        
        for(let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
            
            if (source.checked) {
                checkboxes[i].closest('tr').classList.add('highlighted');
            } else {
                checkboxes[i].closest('tr').classList.remove('highlighted');
            }
        }
        
        if (source.checked && checkboxes.length > 0) {
            const primerAlmacen = checkboxes[0].getAttribute('data-almacen');
            idAlmacenInput.value = primerAlmacen;
        } else {
            idAlmacenInput.value = '';
        }
        
        verificarCheckboxes();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.getElementsByName('marcar[]');
        const idAlmacenInput = document.getElementById('IdAlmacen');
        
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
            $('#modal-container').load('ProcesoIngresos/Ingresos.php?CodBarras=' + id, function() {
                $('#EditarIngreso').modal('show');
                
                $(document).off('click.modal-close').on('click.modal-close', 
                    '[data-dismiss="modal"], .btn-close, .modal-close', 
                    function() {
                        $('#EditarIngreso').modal('hide');
                    }
                );
            });
        });
        
        $(document).on('hidden.bs.modal', '.modal', function () {
            $(this).remove();
        });
    });

    async function seleccionarImpresora(button) {
        try {
            const loading = document.createElement('div');
            loading.textContent = 'Cargando impresoras...';
            loading.style.position = 'fixed';
            loading.style.top = '50%';
            loading.style.left = '50%';
            loading.style.transform = 'translate(-50%, -50%)';
            loading.style.padding = '20px';
            loading.style.backgroundColor = 'white';
            loading.style.border = '1px solid #ccc';
            loading.style.borderRadius = '5px';
            loading.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            loading.style.zIndex = '1000';
            document.body.appendChild(loading);
            
            const response = await fetch('obtener_impresoras.php');
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const impresoras = await response.json();
            document.body.removeChild(loading);
            
            if (!impresoras || impresoras.length === 0) {
                alert('No hay impresoras disponibles en la base de datos');
                return;
            }
            
            const modal = document.createElement('div');
            modal.id = 'modal-impresoras';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';
            modal.style.zIndex = '1000';
            
            const contenido = document.createElement('div');
            contenido.style.backgroundColor = 'white';
            contenido.style.padding = '25px';
            contenido.style.borderRadius = '8px';
            contenido.style.width = '350px';
            contenido.style.maxWidth = '90%';
            contenido.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';

            const titulo = document.createElement('h3');
            titulo.textContent = 'Seleccione una impresora';
            titulo.style.marginTop = '0';
            titulo.style.marginBottom = '20px';
            titulo.style.textAlign = 'center';
            titulo.style.color = '#333';
            contenido.appendChild(titulo);

            const select = document.createElement('select');
            select.style.width = '100%';
            select.style.padding = '10px';
            select.style.margin = '10px 0';
            select.style.borderRadius = '4px';
            select.style.border = '1px solid #ddd';
            select.style.fontSize = '16px';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Seleccione una impresora --';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            select.appendChild(defaultOption);

            impresoras.forEach(impresora => {
                const option = document.createElement('option');
                option.value = impresora.NombreImpresora;
                option.textContent = impresora.NombreImpresora;
                select.appendChild(option);
            });
            contenido.appendChild(select);

            const cantidadLabel = document.createElement('label');
            cantidadLabel.textContent = 'Cantidad de copias:';
            cantidadLabel.style.display = 'block';
            cantidadLabel.style.marginTop = '15px';
            cantidadLabel.style.marginBottom = '5px';
            cantidadLabel.style.fontWeight = '600';
            contenido.appendChild(cantidadLabel);

            const cantidadInput = document.createElement('input');
            cantidadInput.type = 'number';
            cantidadInput.min = '1';
            cantidadInput.value = '1';
            cantidadInput.style.width = '100%';
            cantidadInput.style.padding = '10px';
            cantidadInput.style.borderRadius = '4px';
            cantidadInput.style.border = '1px solid #ddd';
            cantidadInput.style.fontSize = '16px';
            contenido.appendChild(cantidadInput);

            const botonera = document.createElement('div');
            botonera.style.display = 'flex';
            botonera.style.justifyContent = 'flex-end';
            botonera.style.gap = '10px';
            botonera.style.marginTop = '20px';
            
            const btnCancelar = document.createElement('button');
            btnCancelar.textContent = 'Cancelar';
            btnCancelar.onclick = () => {
                document.body.removeChild(modal);
                console.log('Selección cancelada por el usuario');
            };
            
            const btnAceptar = document.createElement('button');
            btnAceptar.textContent = 'Aceptar';
            btnAceptar.id = 'btn-aceptar';

            btnAceptar.onclick = () => {
                if (!select.value) {
                    alert('Por favor seleccione una impresora');
                    return;
                }

                const cantidad = cantidadInput.value || 1; 

                // Obtener los datos del formulario original
                const formOriginal = button.closest('form');
                const codBarras = formOriginal.querySelector('#CodBarrasNum').value;
                const almacen = formOriginal.querySelector('#Almacen').value;

                // Crear formulario dinámico para enviar a GenerarDocQr.php
                const formDinamico = document.createElement('form');
                formDinamico.method = 'POST';
                formDinamico.action = 'GenerarDocQr.php';
                formDinamico.style.display = 'none';

                // Agregar los campos necesarios
                const campos = [
                    { name: 'CodBarras', value: codBarras },
                    { name: 'IdAlmacen', value: almacen },
                    { name: 'Cantidad', value: cantidad },
                    { name: 'NombreImpresora', value: select.value },
                    { name: 'Ventana', value: 'IngresosPendientes.php' }
                ];

                campos.forEach(campo => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = campo.name;
                    input.value = campo.value;
                    formDinamico.appendChild(input);
                });

                document.body.appendChild(formDinamico);
                document.body.removeChild(modal);
                formDinamico.submit();
            };

            const buttonStyles = {
                padding: '10px 20px',
                border: 'none',
                borderRadius: '4px',
                cursor: 'pointer',
                fontSize: '14px',
                fontWeight: '600',
                transition: 'all 0.3s ease'
            };
            
            Object.assign(btnCancelar.style, buttonStyles, {
                backgroundColor: '#f1f1f1',
                color: '#333',
                border: '1px solid #ddd'
            });
            
            Object.assign(btnAceptar.style, buttonStyles, {
                backgroundColor: '#4CAF50',
                color: 'white'
            });
            
            btnCancelar.onmouseover = () => btnCancelar.style.backgroundColor = '#e7e7e7';
            btnCancelar.onmouseout = () => btnCancelar.style.backgroundColor = '#f1f1f1';
            
            btnAceptar.onmouseover = () => btnAceptar.style.backgroundColor = '#45a049';
            btnAceptar.onmouseout = () => btnAceptar.style.backgroundColor = '#4CAF50';
            
            btnAceptar.disabled = true;
            select.addEventListener('change', () => {
                btnAceptar.disabled = !select.value;
            });
            
            botonera.appendChild(btnCancelar);
            botonera.appendChild(btnAceptar);
            contenido.appendChild(botonera);
            
            modal.appendChild(contenido);
            document.body.appendChild(modal);
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar las impresoras: ' + error.message);
        }
    }
</script>

<?php

    if(isset($_POST['Mov']))
    {
        switch($_POST['Mov'])
        {
           case 'ModificarIngreso':
            ModificarIngreso();
          break;
           case 'EnviarSAP': 
           EnviarSAP();
          break;
          case 'ImprimirQR': 
           ImprimirQR();
          break;
        }
    }

    function ModificarIngreso()
    {   
      $rutaServidor = getenv('DB_HOST');
      $nombreBaseDeDatos = getenv('DB');
      $usuario = getenv('DB_USER');
      $contraseña = getenv('DB_PASS');

      try {
        $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
        $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $ZonaHoraria = getenv('ZonaHoraria');
        date_default_timezone_set($ZonaHoraria);

        $fecha = date('Ymd');
        $fechahora = date('Ymd H:i:s');
        $usuario = (!empty($_POST['user'])) ? $_POST['user'] : NULL;

        // Recibir y validar datos
        $IdTarja = (!empty($_POST['IdTarja'])) ? $_POST['IdTarja'] : NULL;
        $IdRemision = (!empty($_POST['IdRemision'])) ? $_POST['IdRemision'] : NULL;
        $CodBarras = (!empty($_POST['CodBarrasNum'])) ? $_POST['CodBarrasNum'] : NULL;
        $IdArticulo = (!empty($_POST['IdArticulo'])) ? $_POST['IdArticulo'] : NULL;
        $FechaProduccion = (!empty($_POST['FechaProduccion'])) ? $_POST['FechaProduccion'] : NULL;
        $FechaIngreso = (!empty($_POST['FechaIngreso'])) ? $_POST['FechaIngreso'] : NULL;
        $Transportista = (!empty($_POST['Transportista'])) ? $_POST['Transportista'] : NULL;
        $Piezas = (!empty($_POST['Piezas'])) ? $_POST['Piezas'] : NULL;
        $NumPedido = (!empty($_POST['NumPedido'])) ? $_POST['NumPedido'] : NULL;
        $NetWeight = (!empty($_POST['NetWeight'])) ? $_POST['NetWeight'] : NULL;
        $GrossWeight = (!empty($_POST['GrossWeight'])) ? $_POST['GrossWeight'] : NULL;
        $Ubicacion = (!empty($_POST['Ubicacion'])) ? $_POST['Ubicacion'] : NULL;
        $PaisOrigen = (!empty($_POST['PaisOrigen'])) ? $_POST['PaisOrigen'] : NULL;
        $Origen = (!empty($_POST['Origen'])) ? $_POST['Origen'] : NULL;
        $NoTarima = (!empty($_POST['NoTarima'])) ? $_POST['NoTarima'] : NULL;
        $Placas = (!empty($_POST['Placas'])) ? $_POST['Placas'] : NULL;
        $Chofer = (!empty($_POST['Chofer'])) ? $_POST['Chofer'] : NULL;
        $Checador = (!empty($_POST['Checador'])) ? $_POST['Checador'] : NULL;
        $Supervisor = (!empty($_POST['Supervisor'])) ? $_POST['Supervisor'] : NULL;
        $Comentarios = (!empty($_POST['Comentarios'])) ? $_POST['Comentarios'] : NULL;

        $EstadoMaterial = (!empty($_POST['EstadoMaterial']) && is_array($_POST['EstadoMaterial'])) 
            ? implode(',', $_POST['EstadoMaterial']) 
            : '';


        if (empty($IdTarja) || empty($IdRemision) || empty($CodBarras) || empty($IdArticulo)) {
            throw new Exception("Faltan campos obligatorios para la modificación");
        }

        // Preparar y ejecutar la consulta de actualización
        $sentencia2 = $Conexion->prepare("UPDATE t_ingreso SET 
            Piezas = ?, FechaIngreso = ?, FechaProduccion = ?, NumPedido = ?, 
            NetWeight = ?, GrossWeight = ?, Origen = ?, IdUbicacion = ?, 
            EstadoMercancia = ?, Transportista = ?, Placas = ?, Chofer = ?, 
            Checador = ?, Supervisor = ?, Estatus = ?, Comentarios = ?, 
            PaisOrigen = ?, NoTarima = ?
            WHERE IdTarja = ? AND IdRemision = ? AND IdArticulo = ? AND CodBarras = ?");

        $resultado2 = $sentencia2->execute([
            $Piezas, $FechaIngreso, $FechaProduccion, $NumPedido, 
            $NetWeight, $GrossWeight, $Origen, $Ubicacion, 
            $EstadoMaterial, $Transportista, $Placas, $Chofer, 
            $Checador, $Supervisor, 3, $Comentarios, 
            $PaisOrigen, $NoTarima, $IdTarja, $IdRemision, $IdArticulo, $CodBarras
        ]);

        if ($resultado2) {   
            $consultaSegura = "UPDATE t_ingreso SET 
                Piezas = $Piezas, FechaIngreso = $FechaIngreso, 
                FechaProduccion = $FechaProduccion, NumPedido = $NumPedido, 
                NetWeight = $NetWeight, GrossWeight = $GrossWeight, 
                Origen = $Origen, IdUbicacion = $Ubicacion, 
                EstadoMercancia = '$EstadoMaterial', Transportista = $Transportista, 
                Placas = $Placas, Chofer = $Chofer, Checador = $Checador, 
                Supervisor = $Supervisor, Estatus = 3, Comentarios = $Comentarios, 
                PaisOrigen = $PaisOrigen, NoTarima = $NoTarima 
                WHERE IdTarja = $IdTarja AND IdRemision = $IdRemision 
                AND IdArticulo = $IdArticulo AND CodBarras = $CodBarras";

            $sentencia = $Conexion->prepare("INSERT INTO t_bitacora 
                (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                VALUES (?, ?, ?, ?, ?)");
                
            $resultado = $sentencia->execute([
                't_ingreso', 
                'Modificar ' . $CodBarras, 
                $fechahora, 
                $consultaSegura, 
                $usuario
            ]);   
                        
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Se ha modificado correctamente',
                        showConfirmButton: false,
                    }).then(function() {
                        window.location = 'IngresosPendientes.php';
                    });
                });
            </script>";
        } else {
            throw new Exception("Error al ejecutar la consulta de actualización");
        }
      } catch (Exception $e) {
          echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error: " . addslashes($e->getMessage()) . "',
                      showConfirmButton: false,
                  }).then(function() {
                      window.location = 'IngresosPendientes.php';
                  });
              });
          </script>";
      } finally {
          $Conexion = null;
      }
    }

    function EnviarSAP()
    {
      $rutaServidor = getenv('DB_HOST');
      $nombreBaseDeDatos = getenv('DB');
      $usuario = getenv('DB_USER');
      $contraseña = getenv('DB_PASS');

      try {
          $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
          $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $ZonaHoraria = getenv('ZonaHoraria');
          date_default_timezone_set($ZonaHoraria);

          $fecha = date('Ymd');
          $fechahora = date('Ymd H:i:s');
          $usuario = (!empty($_POST['user'])) ? $_POST['user'] : NULL;
          $IdAlmacen = (!empty($_POST['IdAlmacen'])) ? $_POST['IdAlmacen'] : NULL;

          if (is_array($_POST['marcar'])) {
              $num_countries = count($_POST['marcar']);
              $current = 0;
              
              foreach ($_POST['marcar'] as $key => $value) {
                  if ($current != $num_countries) {
                      $qry2 = $Conexion->query("SELECT count(*) AS Completo FROM t_ingreso 
                                               WHERE CodBarras = $value 
                                               AND Piezas IS NOT NULL 
                                               AND Origen IS NOT NULL 
                                               AND IdUbicacion IS NOT NULL 
                                               AND EstadoMercancia IS NOT NULL 
                                               AND Transportista IS NOT NULL 
                                               AND Placas IS NOT NULL 
                                               AND Chofer IS NOT NULL 
                                               AND Checador IS NOT NULL 
                                               AND Almacen = $IdAlmacen 
                                               AND Estatus < 4");
                      
                      $DocNot2 = $qry2->fetchAll(PDO::FETCH_OBJ);
                      
                      foreach($DocNot2 as $Not2) {
                          $Completo = $Not2->Completo;

                          if($Completo == 1) {
                              $consulta2 = "UPDATE t_ingreso SET Estatus = 4, Supervisor = $usuario, 
                                           HoraFinal = CASE WHEN HoraFinal IS NULL OR HoraFinal = '' 
                                           THEN '$fechahora' ELSE HoraFinal END 
                                           WHERE CodBarras = $value AND Almacen = $IdAlmacen";

                              $sentencia2 = $Conexion->prepare("UPDATE t_ingreso SET Estatus = ?, Supervisor = ?, 
                                                              HoraFinal = CASE WHEN HoraFinal IS NULL OR HoraFinal = '' 
                                                              THEN ? ELSE HoraFinal END 
                                                              WHERE CodBarras = ? AND Almacen = ?");

                              $resultado2 = $sentencia2->execute([4, $usuario, $fechahora, $value, $IdAlmacen]);

                              if($resultado2) {   
                                  $qry6 = $Conexion->query("SELECT IdRemision FROM t_remision_linea 
                                                           WHERE CodBarras = '$value' AND Almacen = $IdAlmacen");
                                  $DocNot6 = $qry6->fetchAll(PDO::FETCH_OBJ);
                                  
                                  foreach($DocNot6 as $Not6) {
                                      $IdRemision = $Not6->IdRemision;

                                      $consulta6 = "UPDATE t_remision_Encabezado SET Estatus = 4 
                                                   WHERE IdRemision = $IdRemision AND Almacen = $IdAlmacen";

                                      $sentencia6 = $Conexion->prepare("UPDATE t_remision_Encabezado SET Estatus = ? 
                                                                      WHERE IdRemision = ? AND Almacen = ?");

                                      $resultado6 = $sentencia6->execute([4, $IdRemision, $IdAlmacen]);
                                  }

                                  if($resultado6) {   
                                      $sentencia = $Conexion->prepare("INSERT INTO t_bitacora 
                                                                     (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                                                                     VALUES (?, ?, ?, ?, ?)");
                                      $resultado = $sentencia->execute([
                                          't_ingreso',
                                          'EnviarSAP ' . $value, 
                                          $fechahora,
                                          $consulta2,
                                          $usuario
                                      ]);   
                                                   
                                      echo "
                                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                                          <script language='JavaScript'>
                                            document.addEventListener('DOMContentLoaded',function(){
                                              Swal.fire({
                                                    icon: 'success',
                                                    title: 'El Envio se realizo Correctamente',
                                                    showConfirmButton: false,
                                                    }).then(function() {
                                                    window.location =  'IngresosPendientes.php';
                                              });
                                            });
                                        </script>";
                                  } else {
                                     echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                                          <script>
                                          document.addEventListener('DOMContentLoaded', function() {
                                              Swal.fire({
                                                  icon: 'error',
                                                  title: 'Algo ha salido mal, intenta de nuevo',
                                                  showConfirmButton: false
                                              }).then(function() {
                                                  window.location = 'IngresosPendientes.php';
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
          $Conexion = null;
      }
    }

    function ImprimirQR()
    {
        $rutaServidor = getenv('DB_HOST');
        $nombreBaseDeDatos = getenv('DB');
        $usuario = getenv('DB_USER');
        $contraseña = getenv('DB_PASS');

        try {
            $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
            $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!isset($_POST['CodBarrasNum']) || !isset($_POST['Almacen']) || !isset($_POST['NombreImpresora']) || !isset($_POST['CantidadCopias'])) {
                die("Error: Faltan parámetros necesarios (CodBarrasNum, Almacen, NombreImpresora, CantidadCopias)");
            }

            $CodBarras = $_POST['CodBarrasNum'];
            $IdAlmacen = $_POST['Almacen'];
            $NombreImpresora = $_POST['NombreImpresora'];
            $Cantidad = isset($_POST['CantidadCopias']) ? (int)$_POST['CantidadCopias'] : 0;

            if ($Cantidad <= 0) {
                die("Error: La cantidad debe ser un número positivo");
            }

            $ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
            date_default_timezone_set($ZonaHoraria);

            $dir = 'QR/';
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $sentiCliente = $Conexion->prepare("SELECT DISTINCT(t2.NombreCliente) as Cliente, t3.NumRecinto
                                              FROM t_ingreso AS t1 
                                              INNER JOIN dbo.t_cliente AS t2 ON t1.Cliente=t2.IdCliente 
                                              INNER JOIN t_almacen as t3 on t1.Almacen=t3.IdAlmacen
                                              WHERE t1.CodBarras = :codBarras AND t1.Almacen = :idAlmacen");
            $sentiCliente->bindParam(':codBarras', $CodBarras, PDO::PARAM_STR);
            $sentiCliente->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
            $sentiCliente->execute();
            $ClienteInfo = $sentiCliente->fetch(PDO::FETCH_OBJ);

            if (!$ClienteInfo) {
                die("Error: No se encontró información para el código");
            }

            $Cliente = $ClienteInfo->Cliente;
            $NumRecinto = $ClienteInfo->NumRecinto;

            // --- CREAR QR ---
            $filename = $dir . 'CodBarras_' . $CodBarras . '_' . $IdAlmacen . '.png';
            QRcode::png($CodBarras, $filename, 'L', 15, 0);

            // --- CONFIG PDF CON DIMENSIONES CORRECTAS ---
            $pageWidth = 102 * 2.835; // 102 mm en puntos
            $pageHeight = 76 * 2.835; // 76 mm en puntos
            
            $pdf = new TCPDF('L', 'pt', array($pageWidth, $pageHeight), true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(FALSE, 0);
            $pdf->SetFont('helvetica', 'B', 15);

            for ($i = 0; $i < $Cantidad; $i++) {
                $pdf->AddPage();

                $centerX = $pageWidth / 2;
                $centerY = $pageHeight / 2;
                
                $qrSize = 170; 
                $qrX = $centerX - ($qrSize / 2);
                $qrY = $centerY - ($qrSize / 2) - 10; 

                $pdf->Image($filename, $qrX, $qrY, $qrSize, $qrSize, 'PNG', '', '', false, 300);

                $textStartY = $qrY + $qrSize + 5;
                $pdf->SetXY(10, $textStartY);
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->Cell($pageWidth - 20, 0, $NumRecinto . '-' . sprintf("%06d", $CodBarras), 0, 1, 'C');

                $pdf->SetXY(10, $textStartY + 15);
                $pdf->SetFont('helvetica', 'B', 15);
                
                if (strlen($Cliente) > 25) {
                    $partes = wordwrap($Cliente, 25, "|", true);
                    $lineas = explode("|", $partes);
                    
                    $pdf->SetX(10);
                    $pdf->Cell($pageWidth - 20, 0, $lineas[0], 0, 1, 'C');
                    
                    if (isset($lineas[1])) {
                        $pdf->SetXY(10, $textStartY + 24);
                        $pdf->Cell($pageWidth - 20, 0, $lineas[1], 0, 1, 'C');
                    }
                } else {
                    $pdf->SetX(10);
                    $pdf->Cell($pageWidth - 20, 0, $Cliente, 0, 1, 'C');
                }
            }

            // --- GUARDAR TEMPORALMENTE PARA IMPRIMIR ---
            $pdfContent = $pdf->Output('', 'S');
            $tempPdfPath = tempnam(sys_get_temp_dir(), 'tarjas_') . '.pdf';
            file_put_contents($tempPdfPath, $pdfContent);

            // Verificar que SumatraPDF existe
            $sumatraPath = 'C:\\Users\\Administrador\\AppData\\Local\\SumatraPDF\\SumatraPDF.exe';
            if (!file_exists($sumatraPath)) {
                // Intentar rutas alternativas
                $alternativePaths = [
                    'C:\\Program Files\\SumatraPDF\\SumatraPDF.exe',
                    'C:\\Program Files (x86)\\SumatraPDF\\SumatraPDF.exe',
                ];
                
                $sumatraFound = false;
                foreach ($alternativePaths as $altPath) {
                    if (file_exists($altPath)) {
                        $sumatraPath = $altPath;
                        $sumatraFound = true;
                        break;
                    }
                }
                
                if (!$sumatraFound) {
                    throw new Exception("SumatraPDF no encontrado. Verifique la instalación.");
                }
            }

            $printers = shell_exec('wmic printer get name');
            if (strpos($printers, $NombreImpresora) === false) {
                throw new Exception("La impresora '$NombreImpresora' no está instalada o no existe");
            }

            $command = '"' . $sumatraPath . '" -print-to "' . $NombreImpresora . '" "' . $tempPdfPath . '" 2>&1';
            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                $alternativeCommand = 'rundll32.exe printui.dll,PrintUIEntry /k /n "' . $NombreImpresora . '"';
                exec($alternativeCommand, $altOutput, $altReturn);
                
                if ($altReturn === 0) {
                    $printCommand = 'print /d:"' . $NombreImpresora . '" "' . $tempPdfPath . '"';
                    exec($printCommand, $printOutput, $printReturn);
                    
                    if ($printReturn === 0) {
                        $return_var = 0; 
                    }
                }
            }

            // Eliminar archivo temporal después de imprimir
            unlink($tempPdfPath);

            // Limpiar archivo QR temporal
            if (file_exists($filename)) {
                unlink($filename);
            }

            // Limpiar carpeta QR si está vacía
            if (is_dir($dir)) {
                $files = glob($dir . '*');
                if (count($files) === 0) {
                    rmdir($dir);
                }
            }

            if ($return_var === 0) {
                echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded',function(){
                        Swal.fire({
                            icon: 'success',
                            title: 'Se imprimieron $Cantidad copias',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location = 'IngresosPendientes.php';
                        });
                    });
                </script>";
            } else {
                echo "Hubo un error al ejecutar el comando. Código de retorno: " . $return_var;
                echo "<br>Salida del comando: " . implode("\n", $output);
            }
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $Conexion = null;
        }
    }
?>