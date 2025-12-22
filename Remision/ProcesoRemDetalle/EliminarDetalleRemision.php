<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    if (!isset($_POST['Mov']) || $_POST['Mov'] !== 'eliminarDetalle') {
        throw new Exception('Movimiento no válido');
    }

    $IdRemision = isset($_POST['IdRemision']) ? trim($_POST['IdRemision']) : '';
    $IdRemisionEncabezado = isset($_POST['IdRemisionEncabezado']) ? intval($_POST['IdRemisionEncabezado']) : 0;
    $IdLinea = isset($_POST['IdLinea']) ? intval($_POST['IdLinea']) : 0;
    $IdArticulo = isset($_POST['IdArticulo']) ? intval($_POST['IdArticulo']) : 0;
    $IdUsuario = isset($_POST['IdUsuario']) ? intval($_POST['IdUsuario']) : 0;
    $Cliente = isset($_POST['Cliente']) ? intval($_POST['Cliente']) : 0;
    $Almacen = isset($_POST['IdAlmacen']) ? intval($_POST['IdAlmacen']) : 0;
    $Comentarios = isset($_POST['Comentarios']) ? trim($_POST['Comentarios']) : '';

    if (empty($IdRemision) || $IdRemisionEncabezado <= 0) {
        throw new Exception('Datos de remisión inválidos');
    }

    if ($IdLinea <= 0) {
        throw new Exception('ID de línea inválido');
    }

    if ($IdArticulo <= 0) {
        throw new Exception('Artículo inválido');
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

    $stmt = $Conexion->prepare("
        SELECT IdRemisonLinea, Piezas, Cliente, Almacen, IdArticulo
        FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? AND IdRemision = ? AND IdLinea = ?
    ");
    $stmt->execute([$IdRemisionEncabezado, $IdRemision, $IdLinea]);
    $lineaActual = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$lineaActual) {
        throw new Exception('La línea a eliminar no existe');
    }

    if ($lineaActual->IdArticulo != $IdArticulo) {
        throw new Exception('El artículo de la línea no coincide');
    }

    $Conexion->beginTransaction();

    // Guardar en historial antes de eliminar
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
            Usuario
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?)
    ");

    $resultadoHistorial = $stmtHistorial->execute([
        $lineaActual->IdRemisonLinea,
        $IdRemision,
        $IdLinea,
        $IdArticulo,
        $lineaActual->Piezas,
        $Cliente,
        $Almacen,
        $IdRemisionEncabezado,
        'DELETE',
        $Comentarios,
        $IdUsuario
    ]);

    if (!$resultadoHistorial) {
        throw new Exception('Error al guardar el historial de eliminación para la línea ' . $IdLinea);
    }

    // Eliminar la línea de remisión
    $stmtDelete = $Conexion->prepare("
        DELETE FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? 
        AND IdRemision = ? 
        AND IdLinea = ?
        AND IdRemisonLinea = ?
    ");

    $resultado = $stmtDelete->execute([
        $IdRemisionEncabezado,
        $IdRemision,
        $IdLinea,
        $lineaActual->IdRemisonLinea,
    ]);

    if (!$resultado) {
        throw new Exception('Error al eliminar el detalle en la línea ' . $IdLinea);
    }

    // Verificar si fue realmente eliminado
    $stmtVerify = $Conexion->prepare("
        SELECT COUNT(*) as existe 
        FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? 
        AND IdRemision = ? 
        AND IdLinea = ?
    ");
    $stmtVerify->execute([$IdRemisionEncabezado, $IdRemision, $IdLinea]);
    $verificacion = $stmtVerify->fetch(PDO::FETCH_OBJ);

    if ($verificacion->existe > 0) {
        throw new Exception('No se pudo eliminar la línea ' . $IdLinea);
    }

    // ACTUALIZAR LAS LÍNEAS POSTERIORES - RESTAR 1 A LOS IdLinea MAYORES
    $stmtUpdateLineas = $Conexion->prepare("
        UPDATE t_remision_linea 
        SET IdLinea = IdLinea - 1 
        WHERE IdRemisionEncabezadoRef = ? 
        AND IdRemision = ? 
        AND IdLinea > ?
    ");

    $resultadoUpdate = $stmtUpdateLineas->execute([
        $IdRemisionEncabezado,
        $IdRemision,
        $IdLinea
    ]);

    if (!$resultadoUpdate) {
        throw new Exception('Error al actualizar los números de línea posteriores');
    }

    // Obtener información de las líneas actualizadas para el log
    $stmtLineasActualizadas = $Conexion->prepare("
        SELECT IdLinea, IdArticulo 
        FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? 
        AND IdRemision = ? 
        AND IdLinea >= ?
        ORDER BY IdLinea
    ");
    $stmtLineasActualizadas->execute([$IdRemisionEncabezado, $IdRemision, $IdLinea]);
    $lineasActualizadas = $stmtLineasActualizadas->fetchAll(PDO::FETCH_OBJ);

    $Conexion->commit();

    // Registrar en log
    error_log("Detalle de remisión eliminado - Remisión: $IdRemision, Línea: $IdLinea, Artículo: $IdArticulo, Piezas: {$lineaActual->Piezas}, Usuario: $IdUsuario");
    error_log("Líneas actualizadas después de eliminar - Remisión: $IdRemision, Líneas afectadas: " . count($lineasActualizadas));

    echo json_encode([
        'success' => true,
        'message' => 'Detalle eliminado correctamente y líneas posteriores actualizadas',
        'data' => [
            'IdLinea' => $IdLinea,
            'IdArticulo' => $IdArticulo,
            'MaterialNo' => $MaterialNo,
            'PiezasEliminadas' => $lineaActual->Piezas,
            'IdRemisionLinea' => $lineaActual->IdRemisonLinea,
            'lineasActualizadas' => count($lineasActualizadas),
            'nuevoOrdenLineas' => $lineasActualizadas
        ]
    ]);

} catch (Exception $e) {
    if (isset($Conexion) && $Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    error_log("Error en EliminarDetalleRemision: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>