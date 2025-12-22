<?php
include_once "../templates/head.php";
require_once '../vendor/autoload.php';
?>
<style>
    .highlighted td {
        background: #c3c3c3;
    }

    .modal-fotos {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-fotos-contenido {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 900px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .cerrar-fotos {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .cerrar-fotos:hover {
        color: black;
    }

    .galeria-fotos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .foto-item {
        position: relative;
        cursor: pointer;
    }

    .foto-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }

    .foto-item.seleccionada {
        box-shadow: 0 0 0 3px blue;
    }

    .controles-fotos {
        margin: 15px 0;
        display: flex;
        gap: 10px;
    }

    .modal-content {
        border-radius: 0.5rem;
    }

    .modal-header {
        background-color: #d94f00;
        color: white;
    }

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-label {
        margin-left: 5px;
    }

    #comentarios {
        resize: vertical;
    }

    #correosDestino {
        width: 100%;
    }

    .loading {
        display: none;
        text-align: center;
        padding: 20px;
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
                                    <h1 class="card-title">FOTOGRAFIAS INGRESOS</h1>
                                </div>
                                <div class="card-body">
                                    <div style="max-width: 100%;">
                                        <div style="width: 100%;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <form name="EnviarCorreo" id="EnviarCorreo" action="" method="POST"
                                                        enctype="multipart/form-data">
                                                        <button type="submit" name="Mov" id="Mov"
                                                            class="btn btn-success" value="EnviarCorreo" disabled>Enviar
                                                            Correo</button>
                                                        <input type="hidden" id="user" name="user"
                                                            value="<?php echo $IdUsuario; ?>">

                                                        <?php
                                                        $sentFotografias = $Conexion->query("SELECT 
                                                            DISTINCT(t2.IdTarja) AS IdTarja,
                                                            t3.NombreCliente,
                                                            FORMAT(t2.FechaIngreso, 'dd/MM/yyyy') as FechaIngreso,
                                                            t2.Almacen,
                                                            t5.NumRecinto,
                                                            t1.IdFotografias
                                                        FROM t_fotografias_Encabezado as t1 
                                                        INNER JOIN t_ingreso as t2 on t1.IdTarja=t2.IdTarja
                                                        INNER JOIN t_cliente as t3 on t2.Cliente=t3.IdCliente
                                                        INNER JOIN t_usuario_almacen as t4 on t2.Almacen=t4.IdAlmacen 
                                                        INNER JOIN t_almacen as t5 on t1.Almacen=t5.IdAlmacen
                                                        WHERE t1.Tipo=1 
                                                            AND t4.IdUsuario=$IdUsuario
                                                        GROUP BY t2.IdTarja, t2.FechaIngreso, t3.NombreCliente, t2.Almacen, t5.NumRecinto,t1.IdFotografias");
                                                        $Fotografias = $sentFotografias->fetchAll(PDO::FETCH_OBJ);

                                                        ?>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <section class="pt-2">
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class="table table-bordered table-striped"
                                                                            id="tablaconB">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        <input type="checkbox"
                                                                                            onClick="toggle(this)">
                                                                                    </th>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        Tarja</th>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        Cliente</th>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        Fecha Ingreso</th>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        Ver Fotografias</th>
                                                                                    <th width="auto"
                                                                                        style="color:black; text-align: center;">
                                                                                        Tarja Fotografica</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                foreach ($Fotografias as $Fotos) {
                                                                                    $IdTarja = $Fotos->IdTarja;
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td width="auto"
                                                                                            style="text-align: center;">
                                                                                            <input type="checkbox"
                                                                                                name="marcar[]"
                                                                                                value="<?php echo $Fotos->IdTarja; ?>">
                                                                                        </td>
                                                        </form>
                                                        <td width="auto" style="text-align: center;">
                                                            <?php echo 'ALP' . $Fotos->NumRecinto . '-ING-' . sprintf("%04d", $Fotos->IdTarja); ?>
                                                        </td>
                                                        <td width="auto" style="text-align: center;">
                                                            <?php echo $Fotos->NombreCliente; ?>
                                                        </td>
                                                        <td width="auto" style="text-align: center;">
                                                            <?php echo $Fotos->FechaIngreso; ?>
                                                        </td>
                                                        <td width="auto" style="text-align: center;">
                                                            <button type="button" class="btn btn-outline"
                                                                data-id="<?php echo $IdTarja = $Fotos->IdTarja; ?>"
                                                                data-almacen="<?php echo $Fotos->Almacen; ?>"
                                                                data-idFotografias="<?php echo $Fotos->IdFotografias; ?>"
                                                                onclick="abrirModalFotos('<?php echo $Fotos->IdTarja; ?>','<?php echo $Fotos->Almacen; ?>')">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <form action="GenerarTarjaFotografiaNueva.php" method="POST">
                                                                <input type="hidden" name="IdTarja" value="<?= $IdTarja ?>">
                                                                <input type="hidden" name="IdAlmacen"
                                                                    value="<?= $Fotos->Almacen; ?>">
                                                                <button class="btn btn-danger"
                                                                    style="background-color: #d94f00;" type="submit">
                                                                    Descargar
                                                                </button>
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
    </section>
    </div>
    <?php include_once '../templates/footer.php' ?>
    <aside class="control-sidebar">
    </aside>
    </div>

    <!-- Modal para visualizar fotos -->
    <div id="modalFotos" class="modal-fotos">
        <div class="modal-fotos-contenido">
            <span class="cerrar-fotos">&times;</span>
            <h2>Galería de Fotografías - <span id="tarja-titulo"></span></h2>

            <div class="controles-fotos">
                <button id="agregarFoto" class="btn btn-primary">Agregar Foto</button>
                <button id="quitarFoto" class="btn btn-danger">Quitar Foto Seleccionada</button>
                <input type="file" id="inputFoto" accept="image/*" style="display: none;" multiple>
                <input type="hidden" id="idTarjaActual">
                <input type="hidden" id="idAlmacenActual">
                <input type="hidden" id="idFotografias">
            </div>

            <div class="loading" id="loadingFotos">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p>Subiendo fotos, por favor espere...</p>
            </div>

            <div class="galeria-fotos" id="galeriaFotos">
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para enviar correo -->
    <div id="confirmarEnvioModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar envío de correo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formConfirmacionEnvio">
                        <div class="form-group">
                            <label>Seleccione qué desea enviar:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoEnvio" id="soloIngreso"
                                    value="soloIngreso" checked>
                                <label class="form-check-label" for="soloIngreso">
                                    Solo Tarja Ingreso
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoEnvio" id="soloFotografica"
                                    value="soloFotografica" checked>
                                <label class="form-check-label" for="soloFotografica">
                                    Solo Tarja Fotográfica
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipoEnvio" id="ambasTarjas"
                                    value="ambasTarjas">
                                <label class="form-check-label" for="ambasTarjas">
                                    Tarja de Ingreso y Fotográfica
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comentarios">Comentarios:</label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="correosDestino">Correos destinatarios (separados por coma):</label>
                            <input type="text" class="form-control" id="correosDestino" name="correosDestino" required>
                        </div>

                        <input type="hidden" id="tarjasSeleccionadas" name="tarjasSeleccionadas">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmarEnvioBtn">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<div id="modal-container"></div>

<script type="text/javascript">
    function verificarCheckboxes() {
        const checkboxes = document.getElementsByName('marcar[]');
        const btnEnviarSAP = document.getElementById('Mov');

        const algunoMarcado = Array.from(checkboxes).some(checkbox => checkbox.checked);
        btnEnviarSAP.disabled = !algunoMarcado;
    }

    function toggle(source) {
        const checkboxes = document.getElementsByName('marcar[]');

        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;

            if (source.checked) {
                checkboxes[i].closest('tr').classList.add('highlighted');
            } else {
                checkboxes[i].closest('tr').classList.remove('highlighted');
            }
        }
        verificarCheckboxes();
    }

    function abrirModalFotos(idTarja, idAlmacen) {
        const modal = document.getElementById('modalFotos');
        const titulo = document.getElementById('tarja-titulo');
        const idTarjaInput = document.getElementById('idTarjaActual');
        const idAlmacenInput = document.getElementById('idAlmacenActual');

        titulo.textContent = 'ALP-ING-' + idTarja.toString().padStart(4, '0');
        idTarjaInput.value = idTarja;
        idAlmacenInput.value = idAlmacen;

        cargarFotosTarja(idTarja, idAlmacen);
        modal.style.display = 'block';
    }

    function cargarFotosTarja(idTarja, idAlmacen) {
        $.ajax({
            url: 'ProcesoFotografias/obtener_fotos_tarja.php',
            type: 'GET',
            data: {
                idTarja: idTarja,
                idAlmacen: idAlmacen
            },
            dataType: 'json',
            success: function (response) {
                const galeria = document.getElementById('galeriaFotos');
                galeria.innerHTML = '';

                if (response.fotos && response.fotos.length > 0) {
                    response.fotos.forEach((foto) => {
                        const divFoto = document.createElement('div');
                        divFoto.className = 'foto-item';
                        divFoto.dataset.idFoto = foto.IdFoto;

                        const img = document.createElement('img');
                        img.src = foto.RutaFoto;
                        img.alt = `Foto ${foto.IdFoto}`;

                        divFoto.appendChild(img);
                        divFoto.addEventListener('click', function () {
                            seleccionarFoto(this);
                        });

                        galeria.appendChild(divFoto);
                    });
                } else {
                    galeria.innerHTML = '<p>No hay fotografías para esta tarja.</p>';
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar las fotos:', error);
                alert('Error al cargar las fotos. Por favor intente nuevamente.');
            }
        });
    }

    // También corregir en el evento de subir fotos
    document.getElementById('inputFoto').addEventListener('change', function (e) {
        const idTarja = document.getElementById('idTarjaActual').value;
        const idAlmacen = document.getElementById('idAlmacenActual').value;
        const IdFotografias = document.getElementById('idFotografias').value;
        const archivos = e.target.files;
        const loadingElement = document.getElementById('loadingFotos');
        const galeriaElement = document.getElementById('galeriaFotos');

        if (archivos.length > 0 && idTarja) {
            // Mostrar loading
            loadingElement.style.display = 'block';
            galeriaElement.style.display = 'none';

            const formData = new FormData();
            formData.append('idTarja', idTarja);
            formData.append('IdAlmacen', idAlmacen);
            formData.append('IdFotografias', IdFotografias);

            for (let i = 0; i < archivos.length; i++) {
                formData.append('fotos[]', archivos[i]);
            }

            $.ajax({
                url: 'ProcesoFotografias/subir_foto_tarja.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    // Ocultar loading
                    loadingElement.style.display = 'none';
                    galeriaElement.style.display = 'grid';

                    if (response.success) {
                        mostrarMensaje('success', response.message);
                        cargarFotosTarja(idTarja, idAlmacen); // Pasar ambos parámetros
                    } else {
                        mostrarMensaje('error', response.message);
                    }
                },
                error: function (xhr, status, error) {
                    // Ocultar loading
                    loadingElement.style.display = 'none';
                    galeriaElement.style.display = 'grid';

                    console.error('Error en la petición:', error);
                    mostrarMensaje('error', 'Error al subir las fotos: ' + error);
                }
            });
        } else {
            mostrarMensaje('error', 'Por favor selecciona al menos una foto');
        }

        this.value = '';
    });

    function seleccionarFoto(elemento) {
        document.querySelectorAll('.foto-item').forEach(f => f.classList.remove('seleccionada'));
        elemento.classList.add('seleccionada');
    }

    function mostrarMensaje(tipo, mensaje) {
        // Usar SweetAlert para mostrar mensajes
        if (tipo === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                timer: 5000
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.getElementsByName('marcar[]');

        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('click', function () {
                if (this.checked) {
                    this.closest('tr').classList.add('highlighted');
                } else {
                    this.closest('tr').classList.remove('highlighted');
                }

                verificarCheckboxes();
            });
        }

        verificarCheckboxes();

        const modalFotos = document.getElementById('modalFotos');
        const spanCerrar = document.querySelector('.cerrar-fotos');

        spanCerrar.addEventListener('click', function () {
            modalFotos.style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            if (event.target === modalFotos) {
                modalFotos.style.display = 'none';
            }
        });

        document.getElementById('agregarFoto').addEventListener('click', function () {
            document.getElementById('inputFoto').click();
        });

        document.getElementById('inputFoto').addEventListener('change', function (e) {
            const idTarja = document.getElementById('idTarjaActual').value;
            const idAlmacen = document.getElementById('idAlmacenActual').value;
            const idFotografias = document.getElementById('idFotografias').value;
            const archivos = e.target.files;
            const loadingElement = document.getElementById('loadingFotos');
            const galeriaElement = document.getElementById('galeriaFotos');

            if (archivos.length > 0 && idTarja) {
                // Mostrar loading
                loadingElement.style.display = 'block';
                galeriaElement.style.display = 'none';

                const formData = new FormData();
                formData.append('idTarja', idTarja);
                formData.append('IdAlmacen', idAlmacen);
                formData.append('IdFotografias', IdFotografias);

                for (let i = 0; i < archivos.length; i++) {
                    formData.append('fotos[]', archivos[i]);
                }

                $.ajax({
                    url: 'ProcesoFotografias/subir_foto_tarja.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        loadingElement.style.display = 'none';
                        galeriaElement.style.display = 'grid';

                        if (response.success) {
                            mostrarMensaje('success', response.message);
                            cargarFotosTarja(idTarja);
                        } else {
                            mostrarMensaje('error', response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        loadingElement.style.display = 'none';
                        galeriaElement.style.display = 'grid';

                        console.error('Error en la petición:', error);
                        mostrarMensaje('error', 'Error al subir las fotos: ' + error);
                    }
                });
            } else {
                mostrarMensaje('error', 'Por favor selecciona al menos una foto');
            }

            this.value = '';
        });

        document.getElementById('quitarFoto').addEventListener('click', function () {
            const fotoSeleccionada = document.querySelector('.foto-item.seleccionada');
            if (fotoSeleccionada) {
                const idFoto = fotoSeleccionada.dataset.idFoto;
                const idTarja = document.getElementById('idTarjaActual').value;
                const idAlmacen = document.getElementById('idAlmacenActual').value; 
                 const idFotografias = document.getElementById('idFotografias').value; // Obtener idAlmacen

                if (confirm('¿Estás seguro de que deseas eliminar esta foto permanentemente?')) {
                    $.ajax({
                        url: 'ProcesoFotografias/eliminar_foto_tarja.php',
                        type: 'POST',
                        data: {
                            idTarja: idTarja,
                            idFoto: idFoto,
                            idAlmacen: idAlmacen,
                            idFotografias,idFotografias // Enviar idAlmacen
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                mostrarMensaje('success', response.message);
                                cargarFotosTarja(idTarja, idAlmacen); // Pasar ambos parámetros
                            } else {
                                mostrarMensaje('error', response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            mostrarMensaje('error', 'Error al eliminar la foto: ' + error);
                        }
                    });
                }
            } else {
                mostrarMensaje('error', 'Por favor selecciona una foto para eliminar');
            }
        });


        document.getElementById('EnviarCorreo').addEventListener('submit', function (e) {
            e.preventDefault();

            const checkboxes = document.getElementsByName('marcar[]');
            const tarjasSeleccionadas = [];

            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    tarjasSeleccionadas.push(checkboxes[i].value);
                }
            }

            if (tarjasSeleccionadas.length > 0) {
                $('#tarjasSeleccionadas').val(tarjasSeleccionadas.join(','));
                $('#confirmarEnvioModal').modal('show');
            }
        });

        document.getElementById('confirmarEnvioBtn').addEventListener('click', function () {
            const correos = document.getElementById('correosDestino').value;
            if (!correos) {
                alert('Por favor ingrese al menos un correo destinatario');
                return;
            }

            const formData = new FormData(document.getElementById('formConfirmacionEnvio'));
            formData.append('Mov', 'EnviarCorreo');

            const formPrincipal = document.getElementById('EnviarCorreo');
            const formDataPrincipal = new FormData(formPrincipal);

            for (let [key, value] of formData.entries()) {
                formDataPrincipal.append(key, value);
            }

            $.ajax({
                url: formPrincipal.action,
                type: 'POST',
                data: formDataPrincipal,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#confirmarEnvioModal').modal('hide');
                    document.body.innerHTML += response;
                },
                error: function (xhr, status, error) {
                    alert('Error al enviar el correo: ' + error);
                    $('#confirmarEnvioModal').modal('hide');
                }
            });
        });
    });
</script>