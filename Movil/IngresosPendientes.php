<?php
$ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Monterrey';
date_default_timezone_set($ZonaHoraria);

header('Content-Type: application/json; charset=UTF-8');
$rutaServidor = getenv('DB_HOST');
$nombreBaseDeDatos = getenv('DB');
$usuario = getenv('DB_USER');
$contraseña = getenv('DB_PASS');

$response = ['success' => false, 'message' => '', 'updated' => false];

try {
    $requiredFields = ['IdTarja', 'IdRemision', 'CodBarrasNum', 'IdArticulo', 'IdUsuario'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $fechahora = date('Y-m-d H:i:s');
    $usuarioId = $_POST['IdUsuario'];
    $IdTarja = $_POST['IdTarja'];
    $IdRemision = $_POST['IdRemision'];
    $CodBarras = $_POST['CodBarrasNum'];
    $IdArticulo = $_POST['IdArticulo'];
    $IdAlmacen = $_POST['Almacen'];

    // Datos opcionales con valores por defecto
    $FechaProduccion = !empty($_POST['FechaProduccion']) ? $_POST['FechaProduccion'] : null;
    $FechaIngreso = !empty($_POST['FechaIngreso']) ? $_POST['FechaIngreso'] : null;
    $Piezas = !empty($_POST['Piezas']) ? (int) $_POST['Piezas'] : 0;
    $NumPedido = !empty($_POST['NumPedido']) ? $_POST['NumPedido'] : null;
    $NetWeight = !empty($_POST['NetWeight']) ? (float) $_POST['NetWeight'] : 0;
    $GrossWeight = !empty($_POST['GrossWeight']) ? (float) $_POST['GrossWeight'] : 0;
    $Ubicacion = !empty($_POST['Ubicacion']) ? $_POST['Ubicacion'] : null;
    $PaisOrigen = !empty($_POST['PaisOrigen']) ? $_POST['PaisOrigen'] : null;
    $Origen = !empty($_POST['Origen']) ? $_POST['Origen'] : null;
    $NoTarima = !empty($_POST['NoTarima']) ? $_POST['NoTarima'] : null;
    $Checador = !empty($_POST['Checador']) ? $_POST['Checador'] : null;
    $Comentarios = !empty($_POST['Comentarios']) ? $_POST['Comentarios'] : null;
    $Transportista = !empty($_POST['Transportista']) ? $_POST['Transportista'] : null;
    $Placas = !empty($_POST['Placas']) ? $_POST['Placas'] : null;
    $Chofer = !empty($_POST['Chofer']) ? $_POST['Chofer'] : null;
    $Supervisor = !empty($_POST['Supervisor']) ? $_POST['Supervisor'] : null;
    $EstadoMaterial = !empty($_POST['EstadoMaterial']) ? $_POST['EstadoMaterial'] : null;
    $Alto = !empty($_POST['Alto']) ? (float) $_POST['Alto'] : 0;
    $Ancho = !empty($_POST['Ancho']) ? (float) $_POST['Ancho'] : 0;
    $Largo = !empty($_POST['Largo']) ? (float) $_POST['Largo'] : 0;

    // Primero verificar si el registro existe
    $checkStmt = $conexion->prepare("SELECT COUNT(*) as existe FROM t_ingreso 
        WHERE IdTarja = ? AND IdRemision = ? AND IdArticulo = ? AND CodBarras = ? AND Almacen = ?");
    $checkStmt->execute([$IdTarja, $IdRemision, $IdArticulo, $CodBarras, $IdAlmacen]);
    $existe = $checkStmt->fetch(PDO::FETCH_ASSOC)['existe'];

    if ($existe == 0) {
        throw new Exception("No se encontró el registro para actualizar");
    }

    $sentencia2 = $conexion->prepare("UPDATE t_ingreso SET 
        Piezas = ?, 
        FechaIngreso = ?, 
        FechaProduccion = ?, 
        NumPedido = ?, 
        NetWeight = ?, 
        GrossWeight = ?, 
        PaisOrigen = ?, 
        Origen = ?, 
        NoTarima = ?, 
        IdUbicacion = ?, 
        EstadoMercancia = ?, 
        Checador = ?, 
        Estatus = ?, 
        Comentarios = ?, 
        Completado = 1,
        Alto = ?,
        Ancho = ?,
        Largo = ?
        WHERE IdTarja = ? AND IdRemision = ? AND IdArticulo = ? AND CodBarras = ? AND Almacen = ?");

    $resultado2 = $sentencia2->execute([
        $Piezas,
        $FechaIngreso,
        $FechaProduccion,
        $NumPedido,
        $NetWeight,
        $GrossWeight,
        $PaisOrigen,
        $Origen,
        $NoTarima,
        $Ubicacion,
        $EstadoMaterial,
        $Checador,
        2,
        $Comentarios,
        $Alto,
        $Ancho,
        $Largo,
        $IdTarja,
        $IdRemision,
        $IdArticulo,
        $CodBarras,
        $IdAlmacen
    ]);

    if ($resultado2) {
        $rowsAffected = $sentencia2->rowCount();

        if ($rowsAffected > 0) {
            $consultaSegura = "UPDATE t_ingreso SET Piezas = $Piezas, FechaIngreso = '" . ($FechaIngreso ?: 'NULL') . "', 
            FechaProduccion = '" . ($FechaProduccion ?: 'NULL') . "', NumPedido = '" . ($NumPedido ?: 'NULL') . "', 
            NetWeight = $NetWeight, GrossWeight = $GrossWeight, PaisOrigen = '" . ($PaisOrigen ?: 'NULL') . "', 
            Origen = '" . ($Origen ?: 'NULL') . "', NoTarima = '" . ($NoTarima ?: 'NULL') . "', 
            IdUbicacion = '" . ($Ubicacion ?: 'NULL') . "', EstadoMercancia = '" . ($EstadoMaterial ?: 'NULL') . "', 
            Checador = '" . ($Checador ?: 'NULL') . "', Estatus = 2, Comentarios = '" . ($Comentarios ?: 'NULL') . "', 
            Completado = 1,Alto = $Alto,Ancho = $Ancho,Largo = $Largo WHERE IdTarja = $IdTarja AND 
            IdRemision = $IdRemision AND
             IdArticulo = $IdArticulo AND CodBarras = '$CodBarras'AND Almacen = $IdAlmacen";

            $sentencia = $conexion->prepare("INSERT INTO t_bitacora 
                (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                VALUES (?, ?, ?, ?, ?)");

            $resultadoBitacora = $sentencia->execute([
                't_ingreso',
                'Modificar ' . $CodBarras,
                $fechahora,
                $consultaSegura,
                $usuarioId
            ]);

            $response['success'] = true;
            $response['updated'] = true;
            $response['message'] = 'Se ha Modificado Correctamente.';
            $response['rows_affected'] = $rowsAffected;
        } else {
            $response['message'] = 'No se realizaron cambios en el registro';
            $response['updated'] = false;
        }
    } else {
        throw new Exception('Error al ejecutar la consulta de actualización');
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    $response['success'] = false;
} finally {
    if (isset($conexion)) {
        $conexion = null;
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>