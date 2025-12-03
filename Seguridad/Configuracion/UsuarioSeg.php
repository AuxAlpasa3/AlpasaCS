<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";
  
  $usuario =$_SESSION['usuario'];

 $query = "SELECT max(IdUsuario) as IdUsuario FROM t_usuarios_web;";
              if ($ress = mysqli_query($mysqli, $query)) {
                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdUsuario = $fila["IdUsuario"];        
              }
            }
              $CONT=$IdUsuario+1; 
?>
<div class="container-fluid">
  <h1 style="text-align: right; ">Usuarios de Seguridad</h1>
    <button type="button" data-toggle="modal" data-target="#nuevoUsuario" class="btn btn-primary btn-g" style="background-color:#808080; border-color:#808080;">
      <i class="fa fa-plus">Añadir Nuevo</i>
    </button>
  <section class="pt-2">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="auto"
      style="border-top-color: orange;">
        <thead>
          <tr> 
            <th width="auto" style="color:black; text-align: center;">IdUsuario</th>
            <th width="auto" style="color:black; text-align: center;">Usuario</th>
            <th width="auto" style="color:black; text-align: center;">Descripcion</th>
            <th width="auto" style="color:black; text-align: center;">Fecha de Creación</th>
            <th width="auto" style="color:black; text-align: center;">Estatus</th>
            <th width="auto" style="color:black; text-align: center;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $sql = "SELECT IdUsuario, Usuario, Contrasenia, Descripcion, CreateDate,(CASE when Status=1 then 'Activo' when Status=0 then 'Inactivo' END) as Estatus FROM t_usuarios_seg";
            $result = mysqli_query($mysqli,$sql);
              while($row = mysqli_fetch_assoc($result)):
          ?>
            <tr>
              <td style="text-align: center"><?php echo $ID=$row['IdUsuario'];?></td>
              <td style="text-align: center"><?php echo $acceso=$row['Usuario'];?></td>
              <td style="text-align: center"><?php echo $row['Descripcion'];?></td>
              <td style="text-align: center"><?php echo $row['CreateDate'];?></td>
              <td style="text-align: center"><?php echo $row['Estatus'];?></td>
              <td>
                <button type="button" data-toggle="modal" data-target="#modificarUsuario_<?php echo $ID; ?>_<?php  echo $usuario; ?>" class="btn btn-warning" title="Modificar Usuario">  <i class="fa fa-pen"></i></button>
                <button type="button" name='borrar' onclick="Confirmacionborrar(<?php echo $ID;?>,<?php echo "'".$usuario."'"; ?>,'borrar')" id="borrar" class="btn btn-danger" title="Borrar Usuario"><i class="fa fa-trash"></i></button>
                <button type="button" data-toggle="modal" data-target="#PasswordUsuario_<?php echo $ID; ?>_<?php  echo $usuario; ?>" class="btn btn-info" title="Cambiar Contraseña de Usuario"><i class="fa fa-gear"></i></button>
              </td>
                <?php 
                  include "mod_Usuario_seg/Editar.php"; 
                  include "mod_Usuario_seg/Alta.php"; 
                  include "mod_Usuario_seg/Contrasenia.php"; 
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
                                title: "Estas seguro de eliminar al Usuario?",
                                text: "Este proceso no se puede revertir!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Si,Eliminar",
                                cancelButtonText: "No, Cancelar!",
                                reverseButtons: true,
                              }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                   $.ajax({
                                      url: 'mod_Usuario_seg/procesos.php',
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