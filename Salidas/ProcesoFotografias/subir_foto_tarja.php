<?php
include('../../api/db/conexion.php');

$ZonaHoraria = getenv('ZonaHoraria');
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION');


$idTarja = $_POST['idTarja'];
$fotos = $_FILES['fotos'];
$Almacen = $_POST['IdAlmacen'];

$response = ['success' => false, 'message' => ''];

try {
    $directorioBD = "https://intranet.alpasamx.com/" . $RutaLocal . "/Salidas/Fotografias/Almacen" . $Almacen . "Tarja" . $idTarja . "/";

    $directorio = "../Fotografias/Almacen" . $Almacen . "Tarja" . $idTarja . "/";

    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }



    foreach ($fotos['tmp_name'] as $key => $tmp_name) {
        $query3 = $Conexion->query("SELECT max(NextIdFoto)+1  as IdFoto FROM t_Fotografias where IdTarja=$idTarja and Almacen=$Almacen");
        $foto3 = $query3->fetch(PDO::FETCH_ASSOC);

        $nombreOriginal = $fotos['name'][$key];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        if (!in_array(strtolower($extension), $validExtensions)) {
            continue;
        }

        $nombreArchivo = "Foto" . $foto3['IdFoto'] . "_Tarja" . $idTarja . "_Almacen" . $Almacen . '.' . $extension;

        $rutaCompleta = $directorio . $nombreArchivo;
        $rutaCompletaBD = $directorioBD . $nombreArchivo;

        if (move_uploaded_file($tmp_name, $rutaCompleta)) {
            $stmt = $Conexion->prepare("INSERT INTO t_Fotografias (IdTarja,NombreFoto, RutaFoto, FechaFoto,Tipo, NextIdFoto,Almacen) 
                VALUES (?, ?, ?, GETDATE(), ?, ?, ?)");
            $stmt->execute([$idTarja, $nombreArchivo, $rutaCompletaBD, $FechaSalida, 3, $foto3['IdFoto'], $Almacen]);
        }
    }

    $response['success'] = true;
    $response['message'] = 'Fotos subidas correctamente';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);