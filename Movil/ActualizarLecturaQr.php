<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $Almacen = $_POST['Almacen'];
  $IdUbicacion = $_POST['IdUbicacion'];
  $Lectura = json_decode($_POST['CodBarras'], true);
  $IdRevision = $_POST['IdRevision'];
  $fechahora = date('Ymd H:i:s');

    foreach($Lectura as $lecturas){
      $CodBarras=$lecturas['CodBarras'];
      if (strpos($CodBarras, ":") !== false) {
        $partes = explode(':', $CodBarras);
        $numero = trim($partes[1]);
      } else {
        $numero = trim($CodBarras);
      }

      $sql = "INSERT INTO t_lecturaQR (IdRevision ,FechaRevision ,CodBarras ,IdUbicacion ,Almacen) VALUES (?,?,?,?,?);";
      $sentencia = $Conexion->prepare($sql);
      $resultado = $sentencia->execute([$IdRevision,$fechahora,$numero,$IdUbicacion,$Almacen]);
    }
      if($resultado)
      {
         $sql3 = "UPDATE t_revisionUbicaciones set Estatus=1 where IdRevision=? and IdUbicacion=?;";
          $sentencia3 = $Conexion->prepare($sql3);
          $resultado3 = $sentencia3->execute([$IdRevision,$IdUbicacion]);

          $sql2 = "{CALL sp_ActualizarEstatusLecturas(?, ?)}";
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
              echo json_encode(["success" => "Procedimiento ejecutado pero no devolvió datos"], JSON_UNESCAPED_UNICODE);
          }
      }
  }
?>