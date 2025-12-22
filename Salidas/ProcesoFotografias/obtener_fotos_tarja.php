<?php
require_once '../../vendor/autoload.php';

$rutaServidor = getenv('DB_HOST');
$nombreBaseDeDatos = getenv('DB');
$usuario = getenv('DB_USER');
$contraseña = getenv('DB_PASS');

header('Content-Type: application/json');

try {
    $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $idTarja = $_GET['idTarja'] ?? '';
    $idAlmacen = $_GET['idAlmacen'] ?? '';

    if (empty($idTarja) || empty($idAlmacen)) {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
        exit;
    }

    $query = "SELECT 
                IdFoto,
                IdTarja,
                Almacen,
                RutaFoto,
                FechaFoto,
                Tipo
              FROM t_Fotografias 
              WHERE IdTarja =? AND Almacen =? AND Tipo = 3
              ORDER BY FechaFoto DESC";

    $stmt = $Conexion->prepare($query);
    $stmt->execute([$idTarja, $idAlmacen]);
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fotos as &$foto) {
        if (strpos($foto['RutaFoto'], 'http') !== 0) {
            $foto['RutaCompleta'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($foto['RutaFoto'], '/');
        } else {
            $foto['RutaCompleta'] = $foto['RutaFoto'];
        }
    }

    echo json_encode([
        'success' => true,
        'fotos' => $fotos,
        'total' => count($fotos)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>