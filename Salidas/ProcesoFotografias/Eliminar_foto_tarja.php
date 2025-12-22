<?php
include('../../api/db/conexion.php');

$idFoto = $_POST['idFoto'];
$idTarja = $_POST['idTarja'];
$idAlmacen = $_POST['idAlmacen'];

$ZonaHoraria = getenv('ZonaHoraria');
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION');

try {
    $query = "SELECT RutaFoto FROM t_Fotografias WHERE NextIdFoto = ? AND IdTarja = ? AND Almacen = ? AND Tipo = ?";
    $stmt = $Conexion->prepare($query);
    $stmt->execute([$idFoto, $idTarja, $idAlmacen, 3]);
    $foto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($foto) {
        $baseUrl = 'https://192.168.10.195/' . $RutaLocal . '/';
        $basePath = 'C://xampp/htdocs/' . $RutaLocal . '/';

        $filePath = str_replace($baseUrl, $basePath, $foto['RutaFoto']);

        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("No se pudo eliminar el archivo físico");
            }
        }

        $deleteQuery = "DELETE FROM t_Fotografias WHERE NextIdFoto = ? AND IdTarja = ? AND Almacen = ?";
        $deleteStmt = $Conexion->prepare($deleteQuery);
        $deleteStmt->execute([$idFoto, $idTarja, $idAlmacen]);

        if ($deleteStmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Foto eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró la foto en la base de datos'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Foto no encontrada'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar foto: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>