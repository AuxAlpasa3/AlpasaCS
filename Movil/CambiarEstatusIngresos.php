<?php
header('Content-Type: aplication/json;  charset=UTF-8');
Include '../api/db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $IdTarja = $_POST["IdTarja"];
    $Almacen = $_POST["Almacen"];
  $Checador = $_POST["Checador"];

    $sentencia2 = $Conexion->query("SELECT count(*) as totalIngresos from t_ingreso where IdTarja = $IdTarja  and Almacen=$Almacen;");
    $resultado2 = $sentencia2->fetch(PDO::FETCH_OBJ);
    $totalIngresos = $resultado2->totalIngresos;

    $sentencia3 = $Conexion->query("SELECT count(*) as totalIngresosValidadas from t_ingreso ts 
									where IdTarja = $IdTarja  and Completado = 1 and Almacen=$Almacen;");
    $resultado3 = $sentencia3->fetch(PDO::FETCH_OBJ);
    $totalIngresosValidadas = $resultado3->totalIngresosValidadas;
    
    if ($totalIngresos ==$totalIngresosValidadas){
      $sentencia = $Conexion->prepare("UPDATE t_ingreso SET Estatus = 3, Checador = $Checador , horaFinal= GETDATE() WHERE IdTarja = $IdTarja and Almacen=$Almacen;");
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