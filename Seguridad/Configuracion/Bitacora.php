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
        <h1 style="text-align: right; ">Bitacora de Seguridad</h1>
         <section class="pt-2">
              <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="auto" style="color:black; text-align: center;">IdBitacora</th>
                          <th width="auto" style="color:black; text-align: center;">Tabla</th>
                          <th width="auto" style="color:black; text-align: center;">Tipo del Movimiento</th>
                          <th width="auto" style="color:black; text-align: center;">Fecha</th>
                          <th width="3%" style="color:black; text-align: center;">Consulta</th>
                          <th width="auto" style="color:black; text-align: center;">Usuario</th>
                        </tr>
                      </thead>
                      <tbody>
                         <?php 
                            $sql = "SELECT IdBitacora, Tabla, FolMovimiento, Fecha, Consulta, Usuario FROM bitacora";
                            $result = mysqli_query($mysqli,$sql);
                            while($row = mysqli_fetch_object($result)){
                            ?>
                            <tr>
                                <td style="text-align: center"><?php echo $row->IdBitacora;?></td>
                                <td style="text-align: center"><?php echo $row->Tabla;?></td>
                                <td style="text-align: center"><?php echo $row->FolMovimiento;?></td>
                                <td style="text-align: center"><?php echo $row->Fecha;?></td>
                                <td style="text-align: center"><?php echo $row->Consulta;?></td>
                                <td style="text-align: center"><?php echo $row->Usuario;?></td>
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