<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $IdUbicacion = $_POST['IdUbicacion'];
  $IdRevision = $_POST['IdRevision'];
  $fechahora = date('Ymd H:i:s');
       
        $sql2 = "SELECT Concat('UBICACIÓN: ',t2.Ubicacion,'| EN INVENTARIO: ',TotalInventario,'| EN REVISION: ',TotalRevision,'| COINCIDENTES: ',TotalCoincide,' | NO COINCIDENTES: ',TotalNoCoincide,' | NO EXISTEN: ',TotalNoExisten) as Mensaje
          FROM t_revisionUbicaciones as t1 
          INNER JOIN t_ubicacion as t2 on t1.IdUbicacion=t2.IdUbicacion where t1.IdUbicacion=? and t1.IdRevision=?";
        $sentencia2 = $Conexion->prepare($sql2);
        $sentencia2->execute([$IdUbicacion, $IdRevision]);
        $resultado2 = $sentencia2->fetchAll(PDO::FETCH_OBJ);

          if (!empty($resultado2)) {
              $response = [];
              foreach($resultado2 as $fila) {
                  $response[] = $fila->Mensaje;
              }
              echo json_encode($response, JSON_UNESCAPED_UNICODE);
          } else {
              echo json_encode(["success" => "Favor de Verificar el Estatus de la Ubicacion o Revision"], JSON_UNESCAPED_UNICODE);
          }
      }
  
?>