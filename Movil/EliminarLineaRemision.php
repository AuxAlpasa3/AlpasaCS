<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $IdLinea = $_POST["IdLinea"];
  $IdRemision = $_POST["IdRemision"]; 
  $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
  $Almacen = $_POST['Almacen'];

  $sentencia = $Conexion->prepare("DELETE FROM t_remision_linea WHERE IdLinea = $IdLinea AND IdRemision='$IdRemision' AND IdRemisionEncabezadoRef=$IdRemisionEncabezado and Almacen= $Almacen;");
  $resultado = $sentencia->execute([$IdLinea]);
  if($resultado) {
    $sentencia2 = $Conexion->prepare("UPDATE t_remision_linea SET IdLinea = IdLinea - 1 WHERE IdLinea > ? AND IdRemision = ? AND IdRemisionEncabezadoRef=? and Almacen= ?");
    $resultado2 = $sentencia2->execute([$IdLinea, $IdRemision,$IdRemisionEncabezado,$Almacen]);
    $mensaje = "Eliminado correctamente.";  
  }
  else {
    $mensaje = "Ha ocurrido un error al intentar eliminar, intente de nuevo.";      
  }
}
else{
  $mensaje = "No es POST";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

?>