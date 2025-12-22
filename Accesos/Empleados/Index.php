<?php
    Include_once "../../templates/head.php";
    try {
    function formatSqlServerDate($dateValue) {
        if ($dateValue === null || $dateValue === '') {
            return 'No existe un movimiento';
        }
        
        // Verificar si es una fecha válida
        $dateStr = (string)$dateValue;
        if (strpos($dateStr, '1900-01-01') !== false || 
            strpos($dateStr, '0000-00-00') !== false) {
            return 'No existe un movimiento';
        }
        
        try {
            // Intentar formatear la fecha
            $date = new DateTime($dateStr);
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return 'Fecha inválida';
        }
    }
    
    $sql = "SELECT 
                t1.IdMov,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                CASE 
                    WHEN t1.IdUbicacion = 0 THEN 'SinUbicacion' 
                    ELSE t5.NomCorto 
                END as NomCorto,
                t1.FolMovEnt,
                t1.FolMovEnt as MovEnt,
                t1.FechaEntrada,
                t1.FolMovSal,
                t1.FolMovSal as MovSal,
                t1.FechaSalida,
                t1.tiempo as Tiempo,
                t4.DispN as DispEnt, 
                t4.Foto0 as Foto0Ent,
                t4.Foto1 as Foto1Ent,
                t4.Foto2 as Foto2Ent,
                t4.Foto3 as Foto3Ent,
                t4.Foto4 as Foto4Ent,
                t4.Observaciones as ObsEnt,
                t4.Usuario as UsuarioEnt,
                t4.TiempoMarcaje as TiempoEnt,
                t6.DispN as DispSal, 
                t6.Foto0 as Foto0Sal,
                t6.Foto1 as Foto1Sal,
                t6.Foto2 as Foto2Sal,
                t6.Foto3 as Foto3Sal,
                t6.Foto4 as Foto4Sal,
                t6.Observaciones as ObsSal,
                t6.Usuario as UsuarioSal,
                t6.TiempoMarcaje as TiempoSal
            FROM regentsalper as t1 
            LEFT JOIN t_ubicacion as t5 ON t5.IdUbicacion = t1.IdUbicacion 
            INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
            LEFT JOIN regentper as t4 ON t1.FolMovEnt = t4.FolMov
            LEFT JOIN regsalper as t6 ON t1.FolMovSal = t6.FolMov
            ORDER BY t1.IdMov DESC";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php 
     Include_once  "../../templates/nav.php";
     Include_once  "../../templates/aside.php";
     ?>
    <div class="content-wrapper">
      <section class="content mt-4">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <BR>
              <div class="card">
                <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                  <h1 class="card-title">REGISTRO DE MOVIMIENTOS DE PERSONAL</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">
                      <section class="pt-2">
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped" id="dataTableMovimientos" width="100%" cellspacing="0">
                            <thead>
                              <tr>
                                <th width="auto" style="color:black; text-align: center;">Id Movimiento</th>
                                <th width="auto" style="color:black; text-align: center;">Personal</th>
                                <th width="auto" style="color:black; text-align: center;">Ubicación</th>
                                <th width="auto" style="color:black; text-align: center;">Movimiento Entrada</th>
                                <th width="auto" style="color:black; text-align: center;">Fecha Entrada</th>
                                <th width="auto" style="color:black; text-align: center;">Movimiento Salida</th>
                                <th width="auto" style="color:black; text-align: center;">Fecha Salida</th>
                                <th width="auto" style="color:black; text-align: center;">Tiempo</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                                foreach($rows as $row){
                                  $IdMov = $row->IdMov;
                              ?>
                              <tr>
                                <td style="text-align: center">
                                  <?php echo htmlspecialchars($row->IdMov); ?>
                                </td>
                                <td style="text-align: center">
                                  <?php echo htmlspecialchars($row->Personal); ?>
                                </td>
                                <td style="text-align: center">
                                  <?php 
                                  $badge_class = ($row->NomCorto == 'SinUbicacion') ? 'badge-secondary' : 'badge-info';
                                  ?>
                                  <span class="badge <?php echo $badge_class; ?> p-2" style="font-size: 0.9em; min-width: 100px;">
                                    <?php echo htmlspecialchars($row->NomCorto); ?>
                                  </span>
                                </td>
                                <td style="text-align: center">
                                  <?php if ($row->MovEnt == 0): ?>
                                    <span class="badge badge-warning p-2" style="font-size: 0.9em;">
                                      No existe un movimiento
                                    </span>
                                  <?php else: ?>
                                    <button type="button" data-toggle="modal" data-target="#MovEntrada<?php echo htmlspecialchars($row->IdMov); ?>" class="btn btn-info btn-sm">
                                      <i class="fa fa-eye"></i> Ver Detalle <?php echo htmlspecialchars($row->FolMovEnt); ?>
                                    </button>
                                  <?php endif; ?>
                                </td>
                                <td style="text-align: center">
                                  <?php 
                                  $fechaEntrada = formatSqlServerDate($row->FechaEntrada);
                                  if($fechaEntrada == 'No existe un movimiento' || $fechaEntrada == 'Fecha inválida') {
                                      echo '<span class="badge badge-warning">' . $fechaEntrada . '</span>';
                                  } else {
                                      echo htmlspecialchars($fechaEntrada);
                                  }
                                  ?>
                                </td>
                                <td style="text-align: center">
                                  <?php if ($row->MovSal == 0): ?>
                                    <span class="badge badge-warning p-2" style="font-size: 0.9em;">
                                      No existe un movimiento
                                    </span>
                                  <?php else: ?>
                                    <button type="button" data-toggle="modal" data-target="#MovSalida<?php echo htmlspecialchars($row->IdMov); ?>" class="btn btn-info btn-sm">
                                      <i class="fa fa-eye"></i> Ver Detalle <?php echo htmlspecialchars($row->FolMovSal); ?>
                                    </button>
                                  <?php endif; ?>
                                </td>
                                <td style="text-align: center">
                                  <?php 
                                  $fechaSalida = formatSqlServerDate($row->FechaSalida);
                                  if($fechaSalida == 'No existe un movimiento' || $fechaSalida == 'Fecha inválida') {
                                      echo '<span class="badge badge-warning">' . $fechaSalida . '</span>';
                                  } else {
                                      echo htmlspecialchars($fechaSalida);
                                  }
                                  ?>
                                </td>
                                <td style="text-align: center">
                                  <?php 
                                  if(!empty($row->Tiempo)) {
                                      echo '<span class="badge badge-success p-2" style="font-size: 0.9em;">' . htmlspecialchars($row->Tiempo) . '</span>';
                                  } else {
                                      echo '<span class="badge badge-secondary p-2" style="font-size: 0.9em;">N/A</span>';
                                  }
                                  ?>
                                </td>
                              </tr>
                              
                              <?php 
                              // Incluir modales
                              if (file_exists("Controlador/Personal_Ent.php") && $row->MovEnt != 0) {
                                  include "Controlador/Personal_Ent.php"; 
                              }
                              if (file_exists("Controlador/Personal_Sal.php") && $row->MovSal != 0) {
                                  include "Controlador/Personal_Sal.php"; 
                              }
                              ?>
                              
                              <?php } ?>
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
<?php 
$Conexion = null;
include_once '../../templates/Footer.php';
?>
</body>
</html>
<div id="modal-container"></div>
<script type="text/javascript">
$(document).ready(function() {
    $('#dataTableMovimientos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "autoWidth": false,
        "order": [[0, "desc"]]
    });
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
    });
});
</script>

<style>
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.table {
    font-size: 14px;
}

.table thead th {
    vertical-align: middle;
    font-weight: 600;
    background-color: #f8f9fa;
}

.table tbody td {
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 12px;
}

/* Responsive para badges */
@media (max-width: 768px) {
    .badge {
        font-size: 0.75em !important;
        min-width: 70px !important;
        padding: 4px 8px !important;
    }
    
    .btn-sm {
        padding: 3px 6px;
        font-size: 11px;
    }
    
    .table {
        font-size: 12px;
    }
}
</style>