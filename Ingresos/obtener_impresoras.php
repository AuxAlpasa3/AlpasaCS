<?php
header('Content-Type: application/json');

    Include_once "../templates/sesionp.php";



    $stmt = $Conexion->query("SELECT t1.IdImpresora,TRIM(t1.NombreImpresora) AS NombreImpresora FROM t_impresoras as t1
inner join t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen where t2.IdUsuario=$IdUsuario");
    $impresoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($impresoras);

?>