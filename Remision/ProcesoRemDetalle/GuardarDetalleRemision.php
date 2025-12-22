<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    if (!isset($_POST['Mov']) || $_POST['Mov'] !== 'nuevoDetalle') {
        throw new Exception('Movimiento no válido');
    }

    $IdRemision = isset($_POST['IdRemision']) ? trim($_POST['IdRemision']) : '';
    $IdRemisionEncabezado = isset($_POST['IdRemisionEncabezado']) ? intval($_POST['IdRemisionEncabezado']) : 0;
    $IdLinea = isset($_POST['IdLinea']) ? intval($_POST['IdLinea']) : 0;
    $IdArticulo = isset($_POST['Articulo']) ? intval($_POST['Articulo']) : 0;
    $Piezas = isset($_POST['Piezas']) ? intval($_POST['Piezas']) : 0;
    $Cantidad = isset($_POST['Cantidad']) ? intval($_POST['Cantidad']) : 0;
    $IdUsuario = isset($_POST['IdUsuario']) ? intval($_POST['IdUsuario']) : 0;
    $Cliente = isset($_POST['Cliente']) ? intval($_POST['Cliente']) : 0;
    $Almacen = isset($_POST['IdAlmacen']) ? intval($_POST['IdAlmacen']) : 0;
    $Booking = isset($_POST['Booking']) ? trim($_POST['Booking']) : '';
    $Comentarios = isset($_POST['Comentarios']) ? trim($_POST['Comentarios']) : '';

    // Validaciones básicas
    if (empty($IdRemision) || $IdRemisionEncabezado <= 0) {
        throw new Exception('Datos de remisión inválidos');
    }

    if ($IdLinea <= 0) {
        throw new Exception('ID de línea inválido');
    }

    if ($IdArticulo <= 0) {
        throw new Exception('Debe seleccionar un artículo válido');
    }

    if ($Cantidad <= 0) {
        throw new Exception('La cantidad debe ser mayor a 0');
    }

    if ($Piezas < 0) {
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
        throw new Exception('El artículo seleccionado no existe o no está activo');
    }

    $MaterialNo = $articulo->MaterialNo;

    $stmt = $Conexion->prepare("
        SELECT COUNT(*) as total 
        FROM t_remision_linea 
        WHERE IdRemisionEncabezadoRef = ? AND IdRemision = ? AND IdLinea >= ? AND IdLinea < ?
    ");
    $stmt->execute([$IdRemisionEncabezado, $IdRemision, $IdLinea, $IdLinea + $Cantidad]);
    $lineasExistentes = $stmt->fetch(PDO::FETCH_OBJ);

    if ($lineasExistentes->total > 0) {
        throw new Exception('Ya existen líneas en el rango ' . $IdLinea . ' a ' . ($IdLinea + $Cantidad - 1) . ' para esta remisión');
    }

    $Conexion->beginTransaction();

    $idsInsertados = [];
    $lineasInsertadas = [];

    $stmtLinea = $Conexion->prepare("
        INSERT INTO t_remision_linea (
            IdRemision, 
            IdLinea, 
            IdArticulo,
            Piezas,
            Cliente,
            Almacen,
            IdRemisionEncabezadoRef,
            Booking
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

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

    for ($i = 0; $i < $Cantidad; $i++) {
        $currentLinea = $IdLinea + $i;

        $resultado = $stmtLinea->execute([
            $IdRemision,
            $currentLinea,
            $IdArticulo,
            $Piezas,
            $Cliente,
            $Almacen,
            $IdRemisionEncabezado,
            $Booking
        ]);

        if (!$resultado) {
            throw new Exception('Error al insertar el detalle en la línea ' . $currentLinea);
        }

        $IdRemisionLinea = $Conexion->lastInsertId();
        $idsInsertados[] = $IdRemisionLinea;
        $lineasInsertadas[] = $currentLinea;

        $resultadoHistorial = $stmtHistorial->execute([
            $IdRemisionLinea,
            $IdRemision,
            $currentLinea,
            $IdArticulo,
            $Piezas,
            $Cliente,
            $Almacen,
            $IdRemisionEncabezado,
            'INSERT',
            $Comentarios,
            $IdUsuario,
            $Booking
        ]);

        if (!$resultadoHistorial) {
            throw new Exception('Error al guardar el historial de cambios para la línea ' . $currentLinea);
        }
    }

    $Conexion->commit();

    error_log("Detalles de remisión agregados - Remisión: $IdRemision, Líneas: " . implode(',', $lineasInsertadas) . ", Artículo: $IdArticulo, Cantidad: $Cantidad, Usuario: $IdUsuario");

    echo json_encode([
        'success' => true,
        'message' => 'Se agregaron ' . $Cantidad . ' detalles correctamente',
        'data' => [
            'IdLineaInicial' => $IdLinea,
            'IdLineaFinal' => $IdLinea + $Cantidad - 1,
            'IdArticulo' => $IdArticulo,
            'MaterialNo' => $MaterialNo,
            'Piezas' => $Piezas,
            'Cantidad' => $Cantidad,
            'IdsRemisionLinea' => $idsInsertados,
            'LineasInsertadas' => $lineasInsertadas
        ]
    ]);

} catch (Exception $e) {
    if (isset($Conexion) && $Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    error_log("Error en GuardarDetalleRemision: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>