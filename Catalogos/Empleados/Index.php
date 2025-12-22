<?php
// Archivo principal (por ejemplo: personal.php)
session_start();
$BaseURL = getenv('BaseURL');
$VERSION = getenv('VERSION');

include_once $BaseURL . "/api/db/conexion.php";

// Validación de sesión
if (!isset($_SESSION['current_user' . $VERSION])) {
    header("location:" . $BaseURL . "/api/login/logout.php");
    exit;
}

$session_duration = 30 * 60;
$current_time = time();
if (isset($_SESSION['login_time' . $VERSION]) && ($current_time - $_SESSION['login_time' . $VERSION] > $session_duration)) {
    session_unset();
    session_destroy();
    header('location: ' . $BaseURL . '/api/login/logout.php');
    exit;
} else {
    $_SESSION['login_time' . $VERSION] = $current_time;
}

$user = $_SESSION['current_user' . $VERSION];
$IdUsuario = $_SESSION['idusuario' . $VERSION];

// Consulta de personal
$sentPersonal = $Conexion->query(
    "SELECT t1.IdPersonal,t1.NoEmpleado,t1.RutaFoto,t1.Nombre,t1.ApPaterno,t1.ApMaterno,
    (CASE when t1.Cargo=0 then 'Sin Cargo' else t3.NomCargo END) AS NomCargo, 
    (CASE when t1.Departamento=0 THEN 'SinDepto' else t4.NomDepto END) AS NomDepto,
    (CASE when t1.Empresa=0 then 'SinEmpresa' else t2.NomEmpresa END) AS NomEmpresa,
    (CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' END) as Status,
    (CASE when t1.IdUbicacion=0 then 'SinUbicacion' else t5.NomLargo end) as NomCorto 
    FROM t_Personal as t1 
    LEFT JOIN t_empresa as t2 on t1.Empresa=t2.IdEmpresa 
    LEFT JOIN t_cargo as t3 on t1.Cargo=t3.IdCargo 
    LEFT JOIN t_departamento as t4 on t4.IdDepartamento=t1.Departamento 
    LEFT JOIN t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion
    WHERE NoEmpleado > 0 AND t1.tipoPersonal = 1
    ORDER BY IdPersonal");
    
$Personales = $sentPersonal->fetchAll(PDO::FETCH_OBJ);
$imagenPorDefecto = 'https://intranet.alpasamx.com/regentsalper/imagenes/empleados/Default.jpg';
?>

<?php include_once $BaseURL . "/templates/head.php"; ?>

<br>
<div class="card">
    <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00">
        <h1 class="card-title">CATALOGO DE PERSONAL</h1>
    </div>
    <div class="card-body">
        <button type="button" class="btn-nuevo btn btn-primary btn-g" style="background-color:#d94f00; border-color:#d94f00;">
            <i class="fa fa-plus"></i> Añadir Nuevo
        </button>
        
        <div class="table-responsive pt-2">
            <table class="table table-bordered table-striped" id="dataTablePersonal">
                <thead>
                    <tr>
                        <th>NoEmpleado</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>Empresa</th>
                        <th>Estatus</th>
                        <th>Ubicación</th>
                        <th>Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($Personales as $Personal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($Personal->NoEmpleado); ?></td>
                        <td>
                            <?php if(!empty($Personal->RutaFoto) && filter_var($Personal->RutaFoto, FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($Personal->RutaFoto); ?>" 
                                     width="70" 
                                     height="70" 
                                     alt="Foto de <?php echo htmlspecialchars($Personal->Nombre . ' ' . $Personal->ApPaterno); ?>"
                                     class="employee-photo"
                                     onerror="this.onerror=null; this.src='<?php echo $imagenPorDefecto; ?>';">
                                <br>
                                <small>
                                    <a href="<?php echo htmlspecialchars($Personal->RutaFoto); ?>" 
                                       target="_blank" 
                                       class="view-photo-link">
                                        Ver foto
                                    </a>
                                </small>
                            <?php else: ?>
                                <img src="<?php echo $imagenPorDefecto; ?>" 
                                     width="70" 
                                     height="70" 
                                     alt="Sin foto"
                                     class="employee-photo">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($Personal->Nombre); ?></td>
                        <td><?php echo htmlspecialchars($Personal->ApPaterno); ?></td>
                        <td><?php echo htmlspecialchars($Personal->ApMaterno); ?></td>
                        <td><?php echo htmlspecialchars($Personal->NomCargo); ?></td>
                        <td><?php echo htmlspecialchars($Personal->NomDepto); ?></td>
                        <td><?php echo htmlspecialchars($Personal->NomEmpresa); ?></td>
                        <td>
                            <?php
                            $badge_class = ($Personal->Status == 'Activo' || $Personal->Status == '1') ? 'badge-success' : 'badge-danger';
                            $badge_text = ($Personal->Status == 'Activo' || $Personal->Status == '1') ? 'Activo' : 'Inactivo';
                            ?>
                            <span class="badge <?php echo $badge_class; ?> p-2">
                                <?php echo $badge_text; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($Personal->NomCorto); ?></td>
                        <td>
                            <a href="GenerarDoc?ID=<?php echo $Personal->IdPersonal; ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-download"></i> Descargar
                            </a>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-editar btn btn-warning btn-sm" data-id="<?php echo $Personal->IdPersonal; ?>">
                                    <i class="fa fa-edit"></i> Editar
                                </button>
                                <button type="button" 
                                        class="btn-cambiar-estatus btn <?php echo ($Personal->Status == 'Activo') ? 'btn-secondary' : 'btn-success'; ?> btn-sm" 
                                        data-id="<?php echo $Personal->IdPersonal; ?>">
                                    <i class="fas fa-exchange-alt"></i> 
                                    <?php echo ($Personal->Status == 'Activo') ? 'Dar de Baja' : 'Activar'; ?>
                                </button>
                                <button type="button" class="btn-eliminar btn btn-danger btn-sm" data-id="<?php echo $Personal->IdPersonal; ?>">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-container"></div>

<?php include_once $BaseURL . '/templates/Footer.php'; ?>

<!-- Scripts necesarios -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#dataTablePersonal').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "autoWidth": false
    });
    
    $(document).on('click', '.btn-nuevo', function() {
        $('#modal-container').load('mod_Personal/Nuevo.php', function() {
            $('#NuevoPersonal').modal('show');
        });
    });
    
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $('#modal-container').load('mod_Personal/Modificar.php?IdPersonal=' + id, function() {
            $('#ModificarPersonal').modal('show');
        });
    });
    
    $(document).on('click', '.btn-cambiar-estatus', function() {
        var id = $(this).data('id');
        $('#modal-container').load('mod_Personal/CambiarEstatus.php?IdPersonal=' + id, function() {
            $('#CambiarEstatusPersonal').modal('show');
        });
    });
    
    $(document).on('click', '.btn-eliminar', function() {
        var id = $(this).data('id');
        $('#modal-container').load('mod_Personal/Eliminar.php?IdPersonal=' + id, function() {
            $('#EliminarPersonal').modal('show');
        });
    });
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('submit', '#formCambiarEstatusPersonal', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.modal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Error al cambiar el estatus: ' + error);
            }
        });
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
    });
});
</script>