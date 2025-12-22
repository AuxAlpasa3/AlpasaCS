<?php
include_once "../../templates/Sesion.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $requiredFields = ['Mov', 'IdRemisionEncabezado', 'IdUsuario', 'Fecha', 'ClienteId'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    $Mov = $_POST['Mov'];
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
    $IdUsuario = $_POST['IdUsuario'];

    if ($Mov !== 'ModificarRemision') {
        throw new Exception('Operación no válida');
    }

    $Fecha = $_POST['Fecha'];
    $ClienteId = $_POST['ClienteId'];
    $Transportista = $_POST['Transportista'] ?? '';
    $Placas = $_POST['Placas'] ?? '';
    $Chofer = $_POST['Chofer'] ?? '';
    $Contenedor = $_POST['Contenedor'] ?? '';
    $Caja = $_POST['Caja'] ?? '';
    $Tracto = $_POST['Tracto'] ?? '';
    $Sellos = $_POST['Sellos'] ?? '';

    $sentCheck = $Conexion->prepare("SELECT IdRemisionEncabezado FROM t_remision_encabezado WHERE IdRemisionEncabezado = ? AND Estatus IN (0,1)");
    $sentCheck->execute([$IdRemisionEncabezado]);
    $existeRemision = $sentCheck->fetch(PDO::FETCH_OBJ);

    if (!$existeRemision) {
        throw new Exception('La remisión no existe o no está activa');
    }

    $Conexion->beginTransaction();

    $sqlUpdate = "UPDATE t_remision_encabezado 
                  SET FechaRemision = ?, 
                      Cliente = ?, 
                      Transportista = ?, 
                      Placas = ?, 
                      Chofer = ?, 
                      Contenedor = ?, 
                      Caja = ?, 
                      Tracto = ?, 
                      Sellos = ?,
                      FechaUltimaModificacion = GETDATE(),
                      UsuarioModifico = ?
                  WHERE IdRemisionEncabezado = ? AND Estatus IN (0,1)";

    $sentUpdate = $Conexion->prepare($sqlUpdate);
    $resultado = $sentUpdate->execute([
        $Fecha,
        $ClienteId,
        $Transportista,
        $Placas,
        $Chofer,
        $Contenedor,
        $Caja,
        $Tracto,
        $Sellos,
        $IdUsuario,
        $IdRemisionEncabezado
    ]);

    if (!$resultado) {
        throw new Exception('Error al actualizar la remisión');
    }

    if ($sentUpdate->rowCount() === 0) {
        throw new Exception('No se encontraron registros para actualizar');
    }

    $sqlLog = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) 
               VALUES ('t_remision_Encabezado','Modificar', GETDATE(), ?, ?)";
    $sentLog = $Conexion->prepare($sqlLog);
    $sentLog->execute([
        "Modificación de remisión #$IdRemisionEncabezado",
        $IdUsuario
    ]);

    $Conexion->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Remisión actualizada exitosamente',
        'id' => $IdRemisionEncabezado
    ]);

} catch (Exception $e) {
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>