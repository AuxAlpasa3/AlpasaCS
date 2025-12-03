<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";
?>
<div class="container-fluid">
         <section class="pt-2">
              <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                      <tr>
                      <th width="auto" style="color:black; text-align: center;"> Id Movimiento</th>
                      <th width="auto" style="color:black; text-align: center;"> IdVeh</th>
                      <th width="auto" style="color:black; text-align: center;"> Ubicación</th>
                      <th width="auto" style="color:black; text-align: center;"> Movimiento Entrada</th>
                      <th width="auto" style="color:black; text-align: center;"> Fecha Entrada</th>
                      <th width="auto" style="color:black; text-align: center;"> Movimiento Salida</th>
                      <th width="auto" style="color:black; text-align: center;"> Fecha Salida</th>
                      <th width="auto" style="color:black; text-align: center;"> Tiempo</th>
                      </tr>
                      </thead>
                      <tbody>
                        <?php 
                            $sql = "Select t1.IdMov,t1.IdVeh, t2.Marca,t2.Modelo,t2.Num_Serie,t2.Placas,t2.Color,t2.Anio,
              (Case when t1.IdUbicacion=0 then 'SinUbicacion' else t5.NomCorto end) as NomCorto, 
              (Case when t1.FolMovEnt=0 then 'No existe un movimiento' else t1.FolMovEnt end) as FolMovEnt,
              FolMovEnt as MovEnt,
              (Case when t1.FechaEntrada=0 then 'No existe un movimiento' else t1.FechaEntrada end) as FechaEntrada, 
              (Case when t1.FolMovSal=0 then 'No existe un movimiento' else t1.FolMovSal end) as FolMovSal,
              t1.FolMovSal as MovSal,
              (Case when t1.FechaSalida=0 then 'No existe un movimiento' else t1.FechaSalida end) as FechaSalida,
              t1.tiempo as Tiempo,
              t4.DispN as DispEnt, t4.Foto0 as Foto0Ent,t4.Foto1 as Foto1Ent,t4.Foto2 as Foto2Ent,t4.Foto3 as Foto3Ent,t4.Foto4 as Foto4Ent,t4.Observaciones as ObsEnt,t4.Usuario as UsuarioEnt,t4.TiempoMarcaje as TiempoEnt,
              t6.DispN as DispSal, t6.Foto0 as Foto0Sal,t6.Foto1 as Foto1Sal,t6.Foto2 as Foto2Sal,t6.Foto3 as Foto3Sal,t6.Foto4 as Foto4Sal,t6.Observaciones as ObsSal,t6.Usuario as UsuarioSal,t6.TiempoMarcaje as TiempoSal
              From regentsalveh as t1 
              LEFT join t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion 
              inner join t_vehiculos as t2 on t1.IdVeh=t2.IdVehiculo
              LEFT join regentveh as t4 on t1.FolMovEnt=t4.FolMov
              LEFT join regsalveh as t6 on t1.FolMovSal=t6.FolMov;";
                            $result = mysqli_query($mysqli,$sql);
                while($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td style="text-align: center"><?php echo $IdMov=$row['IdMov'];?></td>
                    <td style="text-align: center"><?php echo $row['IdVeh'];?></td>
                    <td style="text-align: center"><?php echo $row['NomCorto'];?></td>
                    <td style="text-align: center">
                       <?php 
                            if($row['MovEnt']==0) { 
                              echo $row['FolMovEnt'];
                              }
                              else{
                        ?>
                      <button type="button" data-toggle="modal" data-target="#MovEntrada<?php echo $IdMov;?>" class="btn" > <?php  echo $row['FolMovEnt'];?></i></button>
                       <?php
                              }
                            ?>
                    </td>
                    <td style="text-align: center"><?php echo $row['FechaEntrada'];?></td>
                      <td style="text-align: center">
                       <?php 
                            if($row['MovSal']==0) { 
                              echo $row['FolMovSal'];
                              }
                              else{?>
                         <button type="button" data-toggle="modal" data-target="#MovSalida<?php echo $IdMov;?>" class="btn" ><?php echo $row['FolMovSal'];?></i></button>
                       <?php
                              }
                            ?>
                    </td>
                    <td style="text-align: center"><?php echo $row['FechaSalida'];?></td>
                    <td style="text-align: center"><?php echo $row['Tiempo'];?></td>
                  
              </td>
                <?php 
                  include "Controlador/Vehiculos_Ent.php"; 
                  include "Controlador/Vehiculos_Sal.php"; 
                  endwhile;
                ?>
            
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php
include "../Config/Footer.php";
?>