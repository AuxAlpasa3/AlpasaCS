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
    
    // Consulta simplificada - manejar el formato en PHP
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
    $rows = $stmt->fetchAll();
    
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
                  <h1 class="card-title">CATALOGO DE PERSONAL</h1>
                </div>
                <div class="card-body">
                  <div style="max-width: 100%;">
                    <div style="width: 100%;">
                  <div class="row">
                  <div class="col-12">

<div class="container-fluid">
    <section class="pt-2">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="auto" style="color:black; text-align: center;">Id Movimiento</th>
                        <th width="auto" style="color:black; text-align: center;">Id Personal</th>
                        <th width="auto" style="color:black; text-align: center;">Ubicación</th>
                        <th width="auto" style="color:black; text-align: center;">Movimiento Entrada</th>
                        <th width="auto" style="color:black; text-align: center;">Fecha Entrada</th>
                        <th width="auto" style="color:black; text-align: center;">Movimiento Salida</th>
                        <th width="auto" style="color:black; text-align: center;">Fecha Salida</th>
                        <th width="auto" style="color:black; text-align: center;">Tiempo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td style="text-align: center"><?php echo htmlspecialchars($row['IdMov']); ?></td>
                            <td style="text-align: center"><?php echo htmlspecialchars($row['Personal']); ?></td>
                            <td style="text-align: center"><?php echo htmlspecialchars($row['NomCorto']); ?></td>
                            <td style="text-align: center">
                                <?php if ($row['MovEnt'] == 0): ?>
                                    No existe un movimiento
                                <?php else: ?>
                                    <button type="button" data-toggle="modal" data-target="#MovEntrada<?php echo htmlspecialchars($row['IdMov']); ?>" class="btn">
                                        <?php echo htmlspecialchars($row['FolMovEnt']); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center"><?php echo formatSqlServerDate($row['FechaEntrada']); ?></td>
                            <td style="text-align: center">
                                <?php if ($row['MovSal'] == 0): ?>
                                    No existe un movimiento
                                <?php else: ?>
                                    <button type="button" data-toggle="modal" data-target="#MovSalida<?php echo htmlspecialchars($row['IdMov']); ?>" class="btn">
                                        <?php echo htmlspecialchars($row['FolMovSal']); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center"><?php echo formatSqlServerDate($row['FechaSalida']); ?></td>
                            <td style="text-align: center"><?php echo htmlspecialchars($row['Tiempo']); ?></td>
                        </tr>
                        
                        <?php 
                        // Incluir modales
                        if (file_exists("Controlador/Personal_Ent.php") && $row['MovEnt'] != 0) {
                            include "Controlador/Personal_Ent.php"; 
                        }
                        if (file_exists("Controlador/Personal_Sal.php") && $row['MovSal'] != 0) {
                            include "Controlador/Personal_Sal.php"; 
                        }
                        ?>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php
$Conexion = null;
include "../Templates/Footer.php";
?>