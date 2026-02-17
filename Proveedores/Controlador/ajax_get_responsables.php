<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json');

$sql = "SELECT id, nombre, departamento 
        FROM responsables 
        WHERE estatus = 'activo' 
        ORDER BY nombre";
        
$result = $Conexion->query($sql);
$responsables = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $responsables[] = $row;
    }
}

echo json_encode($responsables);
$Conexion->close();
?>