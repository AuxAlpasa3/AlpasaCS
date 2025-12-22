<?php
Include '../api/db/conexion.php';

// Recibir los parámetros correctamente
$IdTarja = $_POST["IdTarja"];
$IdRemision = $_POST["IdRemisionEncabezado"]; // Cambiado para coincidir con el payload
$Almacen = $_POST["IdAlmacen"];

// La consulta debe usar el campo correcto de la base de datos
$sentencia = $Conexion->prepare("UPDATE t_salida SET Estatus = ? WHERE IdTarja = ? AND IdRemision = ? AND Almacen = ?;");
$resultado = $sentencia->execute([1, $IdTarja, $IdRemision, $Almacen]);

if($resultado){
    $mensaje = "Estatus (Salida) Actualizado Correctamente.";
} else {
    $mensaje = "Error al actualizar el estatus.";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
?>