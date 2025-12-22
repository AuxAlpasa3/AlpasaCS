<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

  $usuario =$_SESSION['usuario'];
?>

<div class="container-fluid">
    <button class="btn btn-primary btn-g" type="button" data-toggle="modal" data-target="#nuevoEmpleado" style="background-color:#d94f00; border-color:#d94f00;"><i class="fa fa-refresh" aria-hidden="true">Actualizar</i>
    </button>
        <section class="pt-2">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th width="auto" style="color:black; text-align: center;">Id</th>
                          <th width="auto" style="color:black; text-align: center;">Folio Maniobra</th>
                          <th width="auto" style="color:black; text-align: center;">Folio Movimiento</th>
                          <th width="auto" style="color:black; text-align: center;">Fecha Creación</th>
                          <th width="auto" style="color:black; text-align: center;">Operador</th>
                          <th width="auto" style="color:black; text-align: center;">Patio</th>
                          <th width="auto" style="color:black; text-align: center;">Placas</th>
                          <th width="auto" style="color:black; text-align: center;">Producto</th>
                          <th width="auto" style="color:black; text-align: center;">Servicio</th>
                          <th width="auto" style="color:black; text-align: center;">Transportista</th>
                          <!--- <th width="auto" style="color:black; text-align: center;">Acciones</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sql = "SELECT IdMov, FolManiobra,FolMov, FechaCreacion, (CASE WHEN Operador IS NULL THEN 'SIN OPERADOR REGISTRADO' ELSE Operador END) AS Operador, Patio, (CASE WHEN Placas IS NULL THEN 'SIN PLACAS REGISTRADAS' ELSE Placas END) AS Placas, Producto, Servicio, (CASE WHEN Transportista IS NULL THEN 'SIN TRANSPORTISTA REGISTRADO' ELSE Transportista END) AS Transportista FROM t_maniobra";
                                $result = mysqli_query($mysqli,$sql);
                            while($row = mysqli_fetch_assoc($result)):
                                ?>
                        <tr>
                            <td style="text-align: center"><?php echo $row['IdMov'];?></td>
                            <td style="text-align: center"><?php echo $row['FolManiobra'];?></td>
                            <td style="text-align: center"><?php echo $row['FolMov'];?></td>
                            <td style="text-align: center"><?php echo $row['FechaCreacion'];?></td>
                            <td style="text-align: center"><?php echo $row['Operador'];?></td>
                            <td style="text-align: center"><?php echo $row['Patio'];?></td>
                            <td style="text-align: center"><?php echo $row['Placas'];?></td>
                            <td style="text-align: center"><?php echo $row['Producto'];?></td>
                            <td style="text-align: center"><?php echo $row['Servicio'];?></td>
                            <td style="text-align: center"><?php echo $row['Transportista'];?></td>
                             <?php  
                                  endwhile;
                                ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

<?php
include "../Config/Footer.php";
?>