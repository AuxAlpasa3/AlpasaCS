<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    if (!isset($_POST['Mov']) || $_POST['Mov'] !== 'modificarDetalle') {
        throw new Exception('Movimiento no válido');
    }

    $IdRemision = isset($_POST['IdRemision']) ? trim($_POST['IdRemision']) : '';
    $IdRemisionEncabezado = isset($_POST['IdRemisionEncabezado']) ? intval($_POST['IdRemisionEncabezado']) : 0;
    $IdLinea = isset($_POST['IdLinea']) ? intval($_POST['IdLinea']) : 0;
    $IdArticulo = isset($_POST['IdArticulo']) ? intval($_POST['IdArticulo']) : 0;
    $Piezas = isset($_POST['Piezas']) ? intval($_POST['Piezas']) : 0;
    $IdUsuario = isset($_POST['IdUsuario']) ? intval($_POST['IdUsuario']) : 0;
    $Cliente = isset($_POST['Cliente']) ? intval($_POST['Cliente']) : 0;
    $Almacen = isset($_POST['IdAlmacen']) ? intval($_POST['IdAlmacen']) : 0;
    $Comentarios = isset($_POST['Comentarios']) ? trim($_POST['Comentarios']) : '';
    $Booking = isset($_POST['Booking']) ? trim($_POST['Booking']) : '';

    if (empty($IdRemision) || $IdRemisionEncabezado <= 0) {
        throw new Exception('Datos de remisión inválidos');
    }

    if ($IdLinea <= 0) {
        throw new Exception('ID de línea inválido');
    }

    if ($IdArticulo <= 0) {
        throw new Exception('Artículo inválido');
    }

    if ($Piezas < 1) {
        throw new Exception('Las piezas no pueden ser negativas');
    }

    $stmt = $Conexion->prepare("SELECT IdRemision FROM t_remision_encabezado WHERE IdRemision = ? AND IdRemisionEncabezado = ?");
    $stmt->execute([$IdRemision, $IdRemisionEncabezado]);
    $remision = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$remision) {
        throw new Exception('La remisión no existe o no es válida');
    }

    $stmt = $Conexion->prepare("
        SELECT t1.IdArticulo, t1.MaterialNo, t1.Material, t1.Shape 
        FROM t_articulo t1 
        WHERE t1.IdArticulo = ?
    ");
    $stmt->execute([$IdArticulo]);
    $articulo = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$articulo) {
        throw new Exception('El artículo seleccionado no existe');
    }

    $MaterialNo = $articulo->MaterialNo;

    // Obtener datos actuales de la línea para el historial
    $stmt = $Conexion->prepare("
        SELECT IdRemisonLinea, Piezas as PiezasAnteriores, Cliente, Almacen, IdArticulo
        FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? AND IdRemision = ? AND IdLinea = ?
    ");
    $stmt->execute([$IdRemisionEncabezado, $IdRemision, $IdLinea]);
    $lineaActual = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$lineaActual) {
        throw new Exception('La línea a modificar no existe');
    }

    // Verificar que el IdArticulo coincide
    if ($lineaActual->IdArticulo != $IdArticulo) {
        throw new Exception('El artículo de la línea no coincide');
    }

    $Conexion->beginTransaction();

    // Actualizar la línea de remisión
    $stmtUpdate = $Conexion->prepare("
        UPDATE t_remision_linea 
        SET Piezas = ?,
        Booking= ?
        WHERE IdRemisionEncabezadoRef = ? 
        AND IdRemision = ? 
        AND IdLinea = ?
    ");

    $resultado = $stmtUpdate->execute([
        $Piezas,
        $Booking,
        $IdRemisionEncabezado,
        $IdRemision,
        $IdLinea
    ]);

    if (!$resultado) {
        throw new Exception('Error al actualizar el detalle en la línea ' . $IdLinea);
    }

    // Registrar en el historial con todos los datos
    $stmtHistorial = $Conexion->prepare("
        INSERT INTO t_remision_linea_historial (
            IdRemisionLinea,
            IdRemision,
            IdLinea,
            IdArticulo,
            Piezas,
            Cliente,
            Almacen,
            IdRemisionEncabezadoRef,
            TipoCambio,
            Comentario,
            FechaCambio,
            Usuario,
            Booking
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?, ?)
    ");

    $resultadoHistorial = $stmtHistorial->execute([
        $lineaActual->IdRemisonLinea,
        $IdRemision,
        $IdLinea,
        $IdArticulo,
        $Piezas,
        $Cliente,
        $Almacen,
        $IdRemisionEncabezado,
        'UPDATE',
        $Comentarios,
        $IdUsuario,
        $Booking
    ]);

    if (!$resultadoHistorial) {
        throw new Exception('Error al guardar el historial de cambios para la línea ' . $IdLinea);
    }

    $Conexion->commit();

    error_log("Detalle de remisión modificado - Remisión: $IdRemision, Línea: $IdLinea, Artículo: $IdArticulo, Piezas: {$lineaActual->PiezasAnteriores} → $Piezas, Usuario: $IdUsuario");

    echo json_encode([
        'success' => true,
        'message' => 'Detalle modificado correctamente',
        'data' => [
            'IdLinea' => $IdLinea,
            'IdArticulo' => $IdArticulo,
            'MaterialNo' => $MaterialNo,
            'PiezasAnteriores' => $lineaActual->PiezasAnteriores,
            'PiezasNuevas' => $Piezas,
            'IdRemisionLinea' => $lineaActual->IdRemisonLinea
        ]
    ]);

} catch (Exception $e) {
    if (isset($Conexion) && $Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    error_log("Error en ModificarDetalleRemision: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>