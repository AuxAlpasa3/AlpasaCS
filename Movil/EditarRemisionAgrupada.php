<?php
header('Content-Type: application/json; charset=UTF-8');
Include '../api/db/conexion.php';

function respondWithError($message) {
    http_response_code(400);
    $Mensaje = ['success' => false, 'message' => $message];
    echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);
    exit;
}

function respondWithSuccess($message) {
    $Mensaje = ['success' => true, 'message' => $message];
    echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_POST['mov'])) {
    switch ($_POST['mov']) {
        case 'INGRESAR':
            INGRESAR();
            break;
        case 'EDITAR': // ✅ AGREGAR ESTE CASO
            EDITAR_REMISION();
            break;
        default:
            respondWithError('Operación no válida');
    }
} else {
    respondWithError('Falta el parámetro mov');
}

function EDITAR_REMISION() {
    try {
        if (empty($_POST['IdRemisionAgrupada']) || empty($_POST['Almacen'])) {
            respondWithError('Faltan campos requeridos: IdRemisionAgrupada o Almacen');
        }

        if (empty($_POST['Transportista']) || empty($_POST['Placas']) || empty($_POST['Chofer'])) {
            respondWithError('Transportista, Placas y Chofer son obligatorios');
        }

          $rutaServidor = getenv('DB_HOST');
        $nombreBaseDeDatos = getenv('DB');
        $usuarioDB = getenv('DB_USER');
        $contraseñaDB = getenv('DB_PASS');

        $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuarioDB, $contraseñaDB);
        $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $ZonaHoraria = getenv('ZonaHoraria');
        date_default_timezone_set($ZonaHoraria);
        $fechahora = date('Ymd H:i:s');

        $IdRemisionAgrupada = $_POST['IdRemisionAgrupada'];
        $Almacen = $_POST['Almacen'];
        $Transportista = $_POST['Transportista'];
        $Placas = $_POST['Placas'];
        $Chofer = $_POST['Chofer'];
        $Contenedor = $_POST['Contenedor'] ?? '';
        $Sellos = $_POST['Sellos'] ?? '';
        $Tracto = $_POST['Tracto'] ?? '';
        $Caja = $_POST['Caja'] ?? '';

        // Procesar piezas
        $Piezas = [];
        if (isset($_POST['Piezas'])) {
            $piezas_json = $_POST['Piezas'];
            $Piezas = json_decode($piezas_json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                respondWithError('Error al decodificar JSON de piezas: ' . json_last_error_msg());
            }
        }

        $Conexion->beginTransaction();

        // 1. Actualizar encabezados de remisión agrupada
        $stmt = $Conexion->prepare(
            "UPDATE t_remision_encabezado 
             SET Transportista = ?, Placas = ?, Chofer = ?, Contenedor = ?, Sellos = ?, Tracto = ?, Caja = ? 
             WHERE IdRemisionAgrupada = ? AND Almacen = ?"
        );
        $resultado = $stmt->execute([
            $Transportista, $Placas, $Chofer, $Contenedor, $Sellos, $Tracto, $Caja, 
            $IdRemisionAgrupada, $Almacen
        ]);

        if (!$resultado) {
            throw new Exception('Error al actualizar las remisiones agrupadas');
        }

        // 2. Actualizar líneas de piezas si se enviaron
        if (is_array($Piezas) && count($Piezas) > 0) {
            foreach ($Piezas as $item) {
                if (isset($item['IdLinea']) && isset($item['Piezas'])) {
                    $IdLinea = $item['IdLinea'];
                    $PiezasCantidad = $item['Piezas'];

                    $stmtUpdate = $Conexion->prepare(
                        "UPDATE t_remision_linea 
                         SET Piezas = ? 
                         WHERE IdLinea = ? AND Almacen = ?"
                    );
                    $resultadoUpdate = $stmtUpdate->execute([
                        $PiezasCantidad, $IdLinea, $Almacen
                    ]);

                    if (!$resultadoUpdate) {
                        throw new Exception('Error al actualizar las piezas de la línea ' . $IdLinea);
                    }
                }
            }
        }

        $Conexion->commit();
        respondWithSuccess('Remisión Modificada Correctamente.');

    } catch (Exception $e) {
        if (isset($Conexion)) {
            $Conexion->rollBack();
        }
        respondWithError($e->getMessage());
    }
}

?>