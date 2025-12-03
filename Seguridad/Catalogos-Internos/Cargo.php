<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

    $Notificacion = (!empty($POST['not']))   ? $POST['not']: NULL;
  
  $usuario =$_SESSION['usuario'];

 $query = "SELECT max(IdCargo) as IdCargo FROM t_cargo;";
              if ($ress = mysqli_query($mysqli, $query)) {
                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdCargo = $fila["IdCargo"];        
              }
            }
              $CONT=$IdCargo+1; 
?>
<div class="container-fluid">
  <h1 style="text-align: right; ">Cargos</h1>
    <button type="button" data-toggle="modal" data-target="#nuevoCargo" class="btn btn-primary btn-g" style="background-color:#808080; border-color:#808080;">
      <i class="fa fa-plus">Añadir Nuevo</i>
    </button>
  <section class="pt-2">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="auto"
      style="border-top-color: orange;">
        <thead>
            <tr> 
                <th width="auto" style="color:black; text-align: center;">Id</th>
                <th width="auto" style="color:black; text-align: center;">Nombre</th>
                <th width="auto" style="color:black; text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = "Select IdCargo, NomCargo from t_cargo";
                $result = mysqli_query($mysqli,$sql);
                while($row = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td width="auto" style="text-align: center"><?php echo $ID=$row['IdCargo'];?></td>
                <td width="auto"style="text-align: center"><?php echo $row['NomCargo'];?></td>
                <td>
                  <button type="button" data-toggle="modal" data-target="#modificarCargo_<?php echo $ID; ?>_<?php  echo $usuario; ?>" class="btn btn-warning" >
                    <i class="fa fa-pen"></i></button>

                   <button type="button" name='borrar' onclick="Confirmacionborrar(<?php echo $ID;?>,<?php echo "'".$usuario."'"; ?>,'borrar')" id="borrar" class="btn btn-danger" ><i class="fa fa-trash"></i></button>
              </td>
                <?php 
                  include "mod_Cargo/Editar.php"; 
                  include "mod_Cargo/Alta.php"; 
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
                                title: "Estas seguro de eliminar el Cargo?",
                                text: "Este proceso no se puede revertir!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Si,Eliminar",
                                cancelButtonText: "No, Cancelar!",
                                reverseButtons: true,
                              }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                   $.ajax({
                                      url: 'mod_Cargo/procesos.php',
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