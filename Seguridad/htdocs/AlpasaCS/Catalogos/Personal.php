<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

    $Notificacion = (!empty($POST['not']))   ? $POST['not']: NULL;
  
  $usuario =$_SESSION['usuario'];

 $query = "SELECT max(IdPersonal) as IdPersonal FROM t_personal;";
              if ($ress = mysqli_query($mysqli, $query)) {
                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdPersonal = $fila["IdPersonal"];        
              }
            }
              $CONT=$IdPersonal+1; 
?>
<div class="container-fluid">
  <h1 style="text-align: right; ">Catalogo de Personal</h1>
    <button type="button" data-toggle="modal" data-target="#nuevoEmpleado" class="btn btn-primary btn-g" style="background-color:#d94f00; border-color:#d94f00;">
      <i class="fa fa-plus">Añadir Nuevo</i>
    </button>
  <section class="pt-2">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="auto"
      style="border-top-color: orange;">
        <thead>
          <tr> 
            <th width="auto" style="color:black; text-align: center;"> IdPersonal</th>
            <th width="auto" style="color:black; text-align: center;"> Foto</th>
            <th width="auto" style="color:black; text-align: center;"> Nombre</th>
            <th width="auto" style="color:black; text-align: center;"> Apellido Paterno</th>
            <th width="auto" style="color:black; text-align: center;"> Apellido Materno</th>
            <th width="auto" style="color:black; text-align: center;"> Cargo</th>
            <th width="auto" style="color:black; text-align: center;"> Departamento</th>
            <th width="auto" style="color:black; text-align: center;"> Empresa</th>
            <th width="auto" style="color:black; text-align: center;"> Estatus</th>
            <th width="auto" style="color:black; text-align: center;"> Ubicación</th>
            <th width="auto" style="color:black; text-align: center;"> Acceso</th>
            <th width="auto" style="color:black; text-align: center;"> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php 
             $sql = "Select t1.IdPersonal,t1.RutaFoto,t1.Nombre,t1.ApPaterno,t1.ApMaterno,(CASE when t1.Cargo=0 then 'Sin Cargo' else t3.NomCargo END) AS NomCargo, (CASE when t1.Departamento=0 THEN 'SinDepto' else t4.NomDepto END) AS NomDepto,(CASE when t1.Empresa=0 then 'SinEmpresa' else t2.NomEmpresa END) AS NomEmpresa,(CASE when t1.Status=1 then 'Activo' when t1.Status=0 then 'Inactivo' END) as Status,(CASE when t1.IdUbicacion=0 then 'SinUbicacion' else t5.NomCorto end) as NomCorto From t_personal as t1 LEFT join t_empresa as t2 on t1.Empresa=t2.IdEmpresa LEFT join t_cargo as t3 on t1.Cargo=t3.IdCargo LEFT join t_departamento as t4 on t4.IdDepartamento=t1.Departamento LEFT join t_ubicacion as t5 on t5.IdUbicacion =t1.IdUbicacion ";
              $result = mysqli_query($mysqli,$sql);
                while($row = mysqli_fetch_assoc($result)):
          ?>
            <tr>
              <td style="text-align: center"><?php echo $ID=$row['IdPersonal'];?></td>
              <td style="text-align: center">
                <img src="<?php echo $row['RutaFoto']?>" width="80" height="80" 
                  value="<?php echo $row['RutaFoto']?>"/></td>
              <td style="text-align: center"><?php echo $Name=$row['Nombre'];?></td>
              <td style="text-align: center"><?php echo $ApP=$row['ApPaterno'];?></td>
              <td style="text-align: center"><?php echo $ApM=$row['ApMaterno'];?></td>
              <td style="text-align: center"><?php echo $row['NomCargo'];?></td>
              <td style="text-align: center"><?php echo $row['NomDepto'];?></td>
              <td style="text-align: center"><?php echo $row['NomEmpresa'];?></td>
              <td style="text-align: center"><?php echo $row['Status'];?></td>
              <td style="text-align: center"><?php echo $row['NomCorto'];?></td>
              <td><a href="GenerarDoc?ID=<?php echo $ID; ?>">Descargar</a></td>
              <td>
                  <button type="button" data-toggle="modal" data-target="#modificarEmpleado_<?php echo $ID; ?>_<?php  echo $usuario; ?>" class="btn btn-warning" >
                    <i class="fa fa-pen"></i></button>

                   <button type="button" name='borrar' onclick="Confirmacionborrar(<?php echo $ID;?>,<?php echo "'".$usuario."'"; ?>,'borrar')" id="borrar" class="btn btn-danger" ><i class="fa fa-trash"></i></button>
              </td>
                <?php 
                  include "mod_personal/Editar.php"; 
                  include "mod_personal/Alta.php"; 
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
                                title: "Estas seguro de eliminar al empleado?",
                                text: "Este proceso no se puede revertir!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Si,Eliminar",
                                cancelButtonText: "No, Cancelar!",
                                reverseButtons: true,
                              }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                   $.ajax({
                                      url: 'mod_personal/procesos.php',
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