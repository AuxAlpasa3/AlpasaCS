<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

       $query = "SELECT max(IdPersonal) as IdPersonal FROM t_personal;";
              if ($ress = mysqli_query($mysqli, $query)) {

                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdPersonal = $fila["IdPersonal"];        
              }
            }
              $IdPersonal=$IdPersonal+1; 
?>

      <div class="container-fluid">
         <section class="pt-2">
              <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                      <tr>
                      <th width="auto" style="color:black; text-align: center;"> Id Movimiento</th>
                      <th width="auto" style="color:black; text-align: center;"> FolManiobra</th>
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
                            $sql = "Select t1.IdMov,t1.FolManiobra , (Case when t1.IdUbicacion is null then 'SinUbicacion' else t5.NomCorto end) as NomCorto, (Case when t1.FolMovEnt=0 then 'No existe un movimiento' else t1.FolMovEnt end) as FolMovEnt, (Case when t1.FechaEntrada=0 then 'No existe un movimiento' else t1.FechaEntrada end) as FechaEntrada, (Case when t1.FolMovSal=0 then 'No existe un movimiento' else t1.FolMovSal end) as FolMovSal, (Case when t1.FechaSalida=0 then 'No existe un movimiento' else t1.FechaSalida end) as FechaSalida,t1.tiempo as Tiempo From regentsalman as t1 LEFT join t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion;";
                            $result = mysqli_query($mysqli,$sql);
                            while($row = mysqli_fetch_object($result)){
                            ?>
                            <tr>
                                <td style="text-align: center"><?php echo $row->IdMov;?></td>
                                <td style="text-align: center"><?php echo $row->FolManiobra;?></td>
                                <td style="text-align: center"><?php echo $row->NomCorto;?></td>
                                <td style="text-align: center"><?php echo $row->FolMovEnt;?></td>
                                <td style="text-align: center"><?php echo $row->FechaEntrada;?></td>
                                <td style="text-align: center"><?php echo $row->FolMovSal;?></td>
                                <td style="text-align: center"><?php echo $row->FechaSalida;?></td>
                                <td style="text-align: center"><?php echo $row->Tiempo;?></td>
                            </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                      </div>
                    </section>
                  </body>
                </div>

<?php
include "../Config/Footer.php";
?>