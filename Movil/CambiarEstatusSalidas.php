<?php
header('Content-Type: aplication/json;  charset=UTF-8');
Include '../api/db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $IdTarja = $_POST["IdTarja"];
    $Almacen = $_POST["Almacen"];
  $Checador = $_POST["Checador"];

    $sentencia2 = $Conexion->query("SELECT count(*) as totalSalidasPartidas from t_salida where IdTarja = $IdTarja and Almacen=$Almacen;");
    $resultado2 = $sentencia2->fetch(PDO::FETCH_OBJ);
    $totalSalidasPartidas = $resultado2->totalSalidasPartidas;

    $sentencia3 = $Conexion->query("SELECT count(*) as totalPartidasValidadas from t_salida ts 
									where IdTarja = $IdTarja and validado = 1 and Almacen=$Almacen;");
    $resultado3 = $sentencia3->fetch(PDO::FETCH_OBJ);
    $totalPartidasValidadas = $resultado3->totalPartidasValidadas;
    
    if ($totalSalidasPartidas ==$totalPartidasValidadas){
      $sentencia = $Conexion->prepare("UPDATE t_salida SET Estatus = 3, Checador = $Checador ,HoraFinal=GETDATE() WHERE IdTarja = $IdTarja and Almacen=$Almacen;");
  	  $resultado = $sentencia->execute([$Checador,$IdTarja,$Almacen]);
      $mensaje = "Estatus Actualizado Correctamente.";
    }else{
    	$mensaje = "No se puede finalizar hasta que todo los códigos estén validados.";  
    }
}
else{
  $mensaje = "No es POST";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

?>