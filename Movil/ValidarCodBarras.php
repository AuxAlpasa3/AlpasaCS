<?php
header('Content-Type: aplication/json;  charset=UTF-8');
Include '../api/db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $CodBarras = $_POST["CodBarras"];
  $Almacen = $_POST["Almacen"];

  $sentencia = $Conexion->prepare("UPDATE t_salida SET Validado = 1, Estatus = 2 WHERE CodBarras = ? and Almacen=?");
  $resultado = $sentencia->execute([$CodBarras,$Almacen]);
  $mensaje = "Validado correctamente";
}
else{
  $mensaje = "No es POST";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

?>