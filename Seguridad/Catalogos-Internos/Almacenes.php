<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

    $Notificacion = (!empty($POST['not']))   ? $POST['not']: NULL;
  
  $usuario =$_SESSION['usuario'];

 $query = "SELECT max(IdUbicacion) as IdUbicacion FROM t_ubicacion;";
              if ($ress = mysqli_query($mysqli, $query)) {
                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdUbicacion  = $fila["IdUbicacion"];        
              }
            }
              $CONT=$IdUbicacion +1; 
?>
 <div class="container-fluid">
  <h1 style="text-align: right;">Almacenes</h1>
        <button class="btn btn-primary btn-g" type="button" data-toggle="modal" data-target="#nuevoAlmacen" style="background-color:#808080; border-color:#808080;"><i class="fa fa-plus">Añadir Nuevo</i>
        </button>
         <section class="pt-2">
              <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="auto" style="color:black; text-align: center;">Id Ubicación</th>
                          <th width="auto" style="color:black; text-align: center;">Nombre Corto</th>
                          <th width="auto" style="color:black; text-align: center;">Nombre Largo</th>
                          <th width="auto" style="color:black; text-align: center;">Ciudad</th>
                          <th width="auto" style="color:black; text-align: center;">Estado</th>
                          <th width="auto" style="color:black; text-align: center;">Pais</th>
                          <th width="auto" style="color:black; text-align: center;">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                         <?php 
                            $sql = "Select IdUbicacion, NomCorto, NomLargo,Ciudad,Estado,Pais from t_ubicacion";
                            $result = mysqli_query($mysqli,$sql);
                            while($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td style="text-align: center"><?php echo $IdU= $row['IdUbicacion'];?></td>
                                <td style="text-align: center"><?php echo $row['NomCorto'];?></td>
                                <td style="text-align: center"><?php echo $row['NomLargo'];?></td>
                                <td style="text-align: center"><?php echo $row['Ciudad'];?></td>
                                <td style="text-align: center"><?php echo $row['Estado'];?></td>
                                <td style="text-align: center"><?php echo $row['Pais'];?></td>
                                <td>
                                <button type="button" data-toggle="modal" data-target="#modificarAlmacen_<?php echo $IdU; ?>_<?php  echo $usuario; ?>" class="btn btn-warning"><i class="fa fa-pen"></i></button>

                               <button type="button" name='borrar' onclick="Confirmacionborrar(<?php echo $IdU;?>,<?php echo "'".$usuario."'"; ?>,'borrar')" id="borrar" class="btn btn-danger" ><i class="fa fa-trash"></i></button>

                                <?php 
                                  include "mod_Almacen/Editar.php"; 
                                  include "mod_Almacen/Alta.php"; 
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
                                title: "Estas seguro de eliminar el Almacen?",
                                text: "Este proceso no se puede revertir!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Si,Eliminar",
                                cancelButtonText: "No, Cancelar!",
                                reverseButtons: true,
                              }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                   $.ajax({
                                      url: 'mod_Almacen/procesos.php',
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
                                            } ,100);
                                         
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