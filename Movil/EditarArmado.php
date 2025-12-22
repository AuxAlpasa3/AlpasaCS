<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/Conexion.php';

date_default_timezone_set('America/Monterrey');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $Armador = trim($_POST['Armador']);
  $NvoCodBarras = $_POST['NvoCodBarras'];
  $IdArmado = $_POST['IdArmado'];
  $IdUbicacion = $_POST['IdUbicacion'];

  $sentencia = $Conexion->prepare("UPDATE t_armado SET IdUbicacion = ? ,Armador = ?, Estatus = 3, FechaArmado = getDate()
                WHERE NvoCodBarras = ? AND IdArmado = ?;");
  $resultado = $sentencia->execute([$IdUbicacion, $Armador, $NvoCodBarras, $IdArmado]);
  if ($resultado) {

      $sentencia = $Conexion->prepare("UPDATE t_ingreso_armado SET IdUbicacion = ? ,Armador = ?, Estatus = 3, FechaArmado = getDate()
                WHERE CodBarras = ? AND IdArmado = ?;");
      $resultado = $sentencia->execute([$IdUbicacion, $Armador, $NvoCodBarras, $IdArmado]);

    $Mensaje = 'Modificado correctamente.';
  }
} else {
  $Mensaje = 'No es POST.';
}

echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);

?>