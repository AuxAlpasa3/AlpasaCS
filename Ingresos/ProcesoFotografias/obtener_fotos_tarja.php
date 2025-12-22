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
                t1.IdFoto,
                t2.IdTarja,
                t2.Almacen,
                t1.RutaFoto,
                t2.FechaIngreso,
                t2.Tipo
              FROM t_fotografias_Detalle as t1
			  INNER JOIN t_fotografias_Encabezado as t2 on t1.IdFotografiaRef=t2.IdFotografias
              WHERE t2.IdTarja =? AND t2.Almacen =? AND t2.Tipo = 1 
              ORDER BY t1.IdFoto DESC";

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