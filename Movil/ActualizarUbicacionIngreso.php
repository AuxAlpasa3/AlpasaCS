<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $Ingresos = json_decode($_POST['Ingresos'], true);
  $IdUbicacion = $_POST['IdUbicacion'];
  $CodBarras = $_POST['CodBarras'];
  $TipoEditar = $_POST['TipoEditar'];
  $GrossWeight = $_POST['GrossWeight'] ?? null;
  $NetWeight = $_POST['NetWeight'] ?? null;
  $NumPedido = $_POST['NumPedido'] ?? null;
  $IdEstadoMaterial = $_POST['IdEstadoMaterial'] ?? null;
  $FechaProduccion = $_POST['FechaProduccion'] ?? null;
   $IdUsuario = $_POST['IdUsuario'] ?? null;
   $Alto = $_POST['Alto'] ?? 0;
   $Ancho = $_POST['Ancho'] ?? 0;
   $Largo = $_POST['Largo'] ?? 0;
   $NoTarima = $_POST['NoTarima'] ?? null;
   $Booking = $_POST['Booking'] ?? null;
     $Origen = $_POST['Origen'] ?? null;
   $PaisOrigen = $_POST['PaisOrigen'] ?? null;

  if (strpos($CodBarras, ":") !== false) {
    $partes = explode(':', $CodBarras);
    $numero = trim($partes[1]);
  } else {
    $numero = trim($CodBarras);
  }

  if ($TipoEditar == 'Multiple'){
    foreach($Ingresos as $ingreso){
      $CodBarras=$ingreso['code'];
      if (strpos($CodBarras, ":") !== false) {
        $partes = explode(':', $CodBarras);
        $numero = trim($partes[1]);
      } else {
        $numero = trim($CodBarras);
      }

        $sentValidar = $Conexion->query("SELECT EsArmado from t_inventario
                where CodBarras=$numero and Almacen=$IdAlmacen");
            $Validar = $sentValidar->fetchAll(PDO::FETCH_OBJ);

         foreach($Validar as $Val){
          $EsArmado=$Val->EsArmado;

          if($EsArmado==1)
          {
              $sql = "UPDATE t_armado SET IdUbicacion = ? WHERE NvoCodBarras = ? and Almacen=$IdAlmacen;";
              $sentencia = $Conexion->prepare($sql);
              $resultado = $sentencia->execute([$IdUbicacion,$numero]);
          }
          else
          {
              $sql = "UPDATE t_ingreso SET IdUbicacion = ? WHERE CodBarras = ? and Almacen=$IdAlmacen;";
              $sentencia = $Conexion->prepare($sql);
              $resultado = $sentencia->execute([$IdUbicacion,$numero]);
          }


        }

    }
  }else
  {

       $sentValidar = $Conexion->query("SELECT EsArmado from t_inventario
                where CodBarras=$numero");
        $Validar = $sentValidar->fetchAll(PDO::FETCH_OBJ);

        foreach($Validar as $Val)
        {
            $EsArmado=$Val->EsArmado;
            if($EsArmado==1)
            {

                $sql = "UPDATE t_armado SET IdUbicacion = ? WHERE NvoCodBarras = ?;";
                $sentencia = $Conexion->prepare($sql);
                $resultado = $sentencia->execute([$IdUbicacion, $numero]);

            }
            else
            {
                 $sql = "UPDATE t_ingreso SET IdUbicacion = ?,GrossWeight = ?, NetWeight = ?, NumPedido = ?, EstadoMercancia = ?, FechaProduccion = ?, Alto= ?, 
                 Ancho=?, Largo=?, NoTarima=?, Booking=?,PaisOrigen=?, Origen=? WHERE CodBarras = ?;";
                $sentencia = $Conexion->prepare($sql);
                $resultado = $sentencia->execute([$IdUbicacion,$GrossWeight, $NetWeight, $NumPedido , $IdEstadoMaterial, 
                $FechaProduccion, $Alto,$Ancho,$Largo,$NoTarima,$Booking, $PaisOrigen,$Origen, $numero]);
            }
        }

  }


  $Mensaje = 'Ubicación Modificada.';

  echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);
}


?>