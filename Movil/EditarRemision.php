<?php
header('Content-Type: application/json; charset=UTF-8');

include '../api/db/Conexion.php';

date_default_timezone_set('America/Monterrey');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y obtener los datos del POST
    $IdRemision = isset($_POST['IdRemision']) ? trim($_POST['IdRemision']) : '';
    $Transportista = isset($_POST['Transportista']) ? trim($_POST['Transportista']) : '';
    $Placas = isset($_POST['Placas']) ? trim($_POST['Placas']) : '';
    $Chofer = isset($_POST['Chofer']) ? trim($_POST['Chofer']) : '';
    $IdUsuario = isset($_POST['IdUsuario']) ? $_POST['IdUsuario'] : '';
    $Almacen = isset($_POST['Almacen']) ? $_POST['Almacen'] : '';
    $IdRemisionEncabezado = isset($_POST['IdRemisionEncabezado']) ? $_POST['IdRemisionEncabezado'] : '';
    $Contenedor = isset($_POST['Contenedor']) ? $_POST['Contenedor'] : '';
    $Sellos = isset($_POST['Sellos']) ? $_POST['Sellos'] : '';
    $Tracto = isset($_POST['Tracto']) ? $_POST['Tracto'] : '';
    $Caja = isset($_POST['Caja']) ? $_POST['Caja'] : '';

    if (empty($IdRemision) || empty($IdRemisionEncabezado) || empty($Almacen)) {
        echo json_encode('Error: Faltan campos obligatorios', JSON_UNESCAPED_UNICODE);
        exit;
    }

    $Piezas = [];
    if (isset($_POST['Piezas'])) {
        $piezas_json = $_POST['Piezas'];
        $Piezas = json_decode($piezas_json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode('Error al decodificar JSON de piezas', JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    try {
        // Iniciar transacción
        $Conexion->beginTransaction();

        // Actualizar encabezado de remisión
        $sentencia = $Conexion->prepare("UPDATE t_remision_encabezado SET Transportista = ?, Placas = ?, Chofer = ?, Contenedor = ?, Sellos = ?, Tracto = ?, Caja = ? WHERE IdRemision = ? AND IdRemisionEncabezado = ? AND Almacen = ?");
        $resultado = $sentencia->execute([$Transportista, $Placas, $Chofer, $Contenedor, $Sellos, $Tracto, $Caja, $IdRemision, $IdRemisionEncabezado, $Almacen]);

        if (!$resultado) {
            throw new Exception('Error al actualizar el encabezado de la remisión');
        }

        // Actualizar las líneas de piezas
        if (is_array($Piezas) && count($Piezas) > 0) {
            foreach ($Piezas as $item) {
                if (isset($item['IdLinea']) && isset($item['Piezas'])) {
                    $IdLinea = $item['IdLinea'];
                    $PiezasCantidad = $item['Piezas'];

                    $sql = "UPDATE t_remision_linea SET Piezas = ? WHERE IdLinea = ? AND IdRemision = ? AND IdRemisionEncabezadoRef = ? AND Almacen = ?";
                    $sentencia2 = $Conexion->prepare($sql);
                    $resultado2 = $sentencia2->execute([$PiezasCantidad, $IdLinea, $IdRemision, $IdRemisionEncabezado, $Almacen]);

                    if (!$resultado2) {
                        throw new Exception('Error al actualizar las piezas');
                    }
                }
            }
        }

        $Conexion->commit();

        $Mensaje = 'Remision Modificada.';

    } catch (Exception $e) {
        $Conexion->rollBack();
        $Mensaje = 'Error: ' . $e->getMessage();
    }
} else {
    $Mensaje = 'Error: Método no permitido. Se requiere POST.';
}

echo json_encode($Mensaje, JSON_UNESCAPED_UNICODE);
?>