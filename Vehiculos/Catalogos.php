<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

    $Notificacion = (!empty($POST['not']))   ?  $POST['not']: NULL;

  $usuario =$_SESSION['usuario'];
$IdVehiculo;
 $query = "SELECT max(IdVehiculo ) as IdVehiculo  FROM t_vehiculos;";
              if ($ress = mysqli_query($mysqli, $query)) {
                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdVehiculo  = $fila["IdVehiculo"];        
              }
            }
              $CONT=$IdVehiculo +1; 
?>
<div class="container-fluid">
  <h4 style="text-align: right; ">Catalogo de Vehiculos</h4>
    <button type="button" data-toggle="modal" data-target="#nuevoVehiculo" class="btn btn-primary btn-g" style="background-color:#d94f00; border-color:#d94f00;">
      <i class="fa fa-plus">Añadir Nuevo</i>
    </button>
  <section class="pt-2">
    <div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr> 
            <th width="auto" style="color:black; text-align: center;"> IdVehiculo</th>
            <th width="auto" style="color:black; text-align: center;"> Foto</th>
            <th width="auto" style="color:black; text-align: center;"> Empleado</th>
            <th width="auto" style="color:black; text-align: center;"> Marca</th>
            <th width="auto" style="color:black; text-align: center;"> Modelo</th>
            <th width="auto" style="color:black; text-align: center;"> Numero de Serie</th>
            <th width="auto" style="color:black; text-align: center;"> Placas</th>
            <th width="auto" style="color:black; text-align: center;"> Año</th>
            <th width="auto" style="color:black; text-align: center;"> Color</th>
            <th width="auto" style="color:black; text-align: center;"> Estatus</th>
            <th width="auto" style="color:black; text-align: center;"> Acceso</th>
            <th width="auto" style="color:black; text-align: center;"> Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $sql = "SELECT LPAD(t1.IdVehiculo, 5,'0') as IdVehiculo,t1.RutaFoto,t1.IdPersonal, Concat(t2.Nombre,' ',t2.ApPaterno,' ',t2.ApMaterno) as empleado ,t1.Marca,t1.Modelo,t1.Num_Serie, t1.Placas,t1.Anio,t1.Color, case when t1.Activo=1 then 'Activo' else 'Inactivo' end as Activo FROM t_vehiculos as t1 inner join t_personal as t2 on t1.IdPersonal=t2.IdPersonal";
                $result = mysqli_query($mysqli,$sql);
                while($row = mysqli_fetch_assoc($result)):
        ?>
        <tr>
            <td style="text-align: center"><?php echo $Idv=$row['IdVehiculo'];?></td>
            <td style="text-align: center">
                <img src="<?php echo $row['RutaFoto']?>" width="80" height="80" 
                  value="<?php echo $row['RutaFoto']?>"/></td>
            <td style="text-align: center"><?php echo $row['empleado'];?></td>
            <td style="text-align: center"><?php echo $row['Marca'];?></td>
            <td style="text-align: center"><?php echo $row['Modelo'];?></td>
            <td style="text-align: center"><?php echo $row['Num_Serie'];?></td>
            <td style="text-align: center"><?php echo $row['Placas'];?></td>
            <td style="text-align: center"><?php echo $row['Anio'];?></td>
            <td style="text-align: center"><?php echo $row['Color'];?></td>
            <td style="text-align: center"><?php echo $row['Activo'];?></td>
            <td><a href="GenerarDocVeh?ID=<?php echo $Idv; ?>">Descargar</a></td>
            <td>
                  <button type="button" data-toggle="modal" data-target="#modificarVehiculo_<?php echo $Idv; ?>_<?php  echo $usuario; ?>" class="btn btn-warning" >
                    <i class="fa fa-pen"></i></button>

                   <button type="button" name='borrar' onclick="Confirmacionborrar(<?php echo $Idv;?>,<?php echo "'".$usuario."'"; ?>,'borrar')" id="borrar" class="btn btn-danger" ><i class="fa fa-trash"></i></button>
              </td>
              <?php 
                  include "mod_vehiculo/Editar.php"; 
                  include "mod_vehiculo/Alta.php"; 
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
<script type="text/javascript">
function Confirmacionborrar(id,usuario,Mov) {
                              const swalWithBootstrapButtons = Swal.mixin({
                                customClass: {
                                  confirmButton: "btn btn-success",
                                  cancelButton: "btn btn-danger"
                                },
                                buttonsStyling: false
                              });
                              swalWithBootstrapButtons.fire({
                                title: "Estas seguro de eliminar el vehiculo?",
                                text: "Este proceso no se puede revertir!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Si,Eliminar",
                                cancelButtonText: "No, Cancelar!",
                                reverseButtons: true,
                              }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                   $.ajax({
                                      url: 'mod_vehiculo/procesos.php',
                                      type: 'POST',
                                      data: {
                                        id: id,
                                        usuario: usuario,
                                        Mov: Mov
                                      }
                                    });  

                                   success: 
                                        Swal.fire({
                                            icon: "success",
                                            title: "Se ha Eliminado Correctamente",
                                            showConfirmButton: false
                                            });
                                          window.setTimeout(function(){ 
                                              location.reload();
                                            },100);
                                         
                                } else if (
                                  result.dismiss === Swal.DismissReason.cancel

                                ) {
                                  swalWithBootstrapButtons.fire({
                                    title: "Cancelado",
                                    text: "Se ha cancelado el proceso",
                                    icon: "error"
                                  });
                                }
                              });
                            } 
</script>