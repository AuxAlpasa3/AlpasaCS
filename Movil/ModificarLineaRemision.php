<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

Include '../api/db/Conexion.php';

$response = array('success' => false, 'message' => '');

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos obligatorios
    if (!isset($data['IdRemisionEncabezado']) || !isset($data['IdRemision']) || 
        !isset($data['IdLinea']) || !isset($data['IdArticulo']) || 
        !isset($data['Piezas']) || !isset($data['IdUsuario']) || !isset($data['Almacen'])) {
        
        $missing = [];
        if (!isset($data['IdRemisionEncabezado'])) $missing[] = 'IdRemisionEncabezado';
        if (!isset($data['IdRemision'])) $missing[] = 'IdRemision';
        if (!isset($data['IdLinea'])) $missing[] = 'IdLinea';
        if (!isset($data['IdArticulo'])) $missing[] = 'IdArticulo';
        if (!isset($data['Piezas'])) $missing[] = 'Piezas';
        if (!isset($data['IdUsuario'])) $missing[] = 'IdUsuario';
        if (!isset($data['Almacen'])) $missing[] = 'Almacen';
        
        throw new Exception('Faltan datos obligatorios: ' . implode(', ', $missing));
    }

    $IdRemisionEncabezado = $data['IdRemisionEncabezado'];
    $IdRemision = $data['IdRemision'];
    $IdLinea = $data['IdLinea'];
    $IdArticulo = $data['IdArticulo'];
    $Booking = isset($data['Booking']) ? $data['Booking'] : '';
    $Piezas = $data['Piezas'];
    $IdUsuario = $data['IdUsuario'];
    $Almacen = $data['Almacen'];

    // Log para debugging
    error_log("Datos recibidos para modificar línea:");
    error_log("IdRemisionEncabezado: " . $IdRemisionEncabezado);
    error_log("IdRemision: " . $IdRemision);
    error_log("IdLinea: " . $IdLinea);
    error_log("IdArticulo: " . $IdArticulo);
    error_log("Booking: " . $Booking);
    error_log("Piezas: " . $Piezas);
    error_log("IdUsuario: " . $IdUsuario);
    error_log("Almacen: " . $Almacen);

    $Conexion->beginTransaction();

    $query = "UPDATE t_remision_linea 
              SET IdArticulo = ?, Booking = ?, Piezas = ?
              WHERE IdRemision = ? AND IdLinea = ? AND IdRemisionEncabezadoRef = ? AND Almacen = ?";
    
    $stmt = $Conexion->prepare($query);
    $stmt->execute([
        $IdArticulo, 
        $Booking, 
        $Piezas, 
        $IdRemision, 
        $IdLinea, 
        $IdRemisionEncabezado,
        $Almacen
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No se encontró la línea para modificar. Verifique los datos: ' . 
                           "Remision: $IdRemision, Línea: $IdLinea, Encabezado: $IdRemisionEncabezado, Almacen: $Almacen");
    }

    // Insertar en historial
    $queryHistorial = "INSERT INTO t_remision_linea_historial 
                      (IdRemisionEncabezadoRef, IdRemision, IdLinea, IdArticulo, Booking, Piezas, Usuario, FechaCambio, TipoCambio)
                      VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE(), 'UPDATE')";
    
    $stmtHistorial = $Conexion->prepare($queryHistorial);
    $stmtHistorial->execute([
        $IdRemisionEncabezado, 
        $IdRemision, 
        $IdLinea, 
        $IdArticulo, 
        $Booking, 
        $Piezas, 
        $IdUsuario
    ]);

    $Conexion->commit();

    $response['success'] = true;
    $response['message'] = 'Línea modificada correctamente.';

} catch (Exception $e) {
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log("Error en ModificarLineaRemision: " . $e->getMessage());
}

echo json_encode($response);
?>