<?php
Include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $IdPersonal = $_POST['IdPersonal'];
        $estatus_nuevo = $_POST['estatus_nuevo'];
        $motivo = trim($_POST['motivo'] ?? '');
        $usuario_id = $_SESSION['usuario_id'] ?? 0; // Ajustar según tu sistema de sesión
        
        // Iniciar transacción
        $Conexion->beginTransaction();
        
        // 1. Actualizar estatus en la tabla t_personal
        $sqlUpdate = "UPDATE t_personal SET 
                     Status = ?, 
                     FechaModificacion = NOW()
                     WHERE IdPersonal = ?";
        
        $stmtUpdate = $Conexion->prepare($sqlUpdate);
        $stmtUpdate->execute([$estatus_nuevo, $IdPersonal]);
        
        // 2. Registrar en el historial de cambios (opcional - crear tabla si no existe)
        /*
        CREATE TABLE IF NOT EXISTS t_historial_estatus (
            IdHistorial INT AUTO_INCREMENT PRIMARY KEY,
            IdPersonal INT NOT NULL,
            EstatusAnterior TINYINT(1) NOT NULL,
            EstatusNuevo TINYINT(1) NOT NULL,
            Motivo TEXT,
            IdUsuario INT,
            FechaCambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (IdPersonal) REFERENCES t_personal(IdPersonal)
        );
        */
        
        if (tableExists($Conexion, 't_historial_estatus')) {
            $estatus_actual = $_POST['estatus_actual'];
            
            $sqlHistorial = "INSERT INTO t_historial_estatus 
                            (IdPersonal, EstatusAnterior, EstatusNuevo, Motivo, IdUsuario) 
                            VALUES (?, ?, ?, ?, ?)";
            
            $stmtHistorial = $Conexion->prepare($sqlHistorial);
            $stmtHistorial->execute([$IdPersonal, $estatus_actual, $estatus_nuevo, $motivo, $usuario_id]);
        }
        
        // Confirmar transacción
        $Conexion->commit();
        
        // Obtener nombre del personal para el mensaje
        $sqlNombre = "SELECT Nombre, ApPaterno, ApMaterno FROM t_personal WHERE IdPersonal = ?";
        $stmtNombre = $Conexion->prepare($sqlNombre);
        $stmtNombre->execute([$IdPersonal]);
        $personal = $stmtNombre->fetch(PDO::FETCH_OBJ);
        
        $nombre_completo = $personal->Nombre . ' ' . $personal->ApPaterno . ' ' . $personal->ApMaterno;
        $nuevo_estatus_texto = ($estatus_nuevo == 1) ? 'Activo' : 'Inactivo';
        
        $_SESSION['success'] = "Estatus cambiado correctamente para: <strong>$nombre_completo</strong><br>Nuevo estatus: <span class='badge badge-" . (($estatus_nuevo == 1) ? 'success' : 'danger') . "'>$nuevo_estatus_texto</span>";
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        if ($Conexion->inTransaction()) {
            $Conexion->rollBack();
        }
        $_SESSION['error'] = "Error al cambiar el estatus: " . $e->getMessage();
    }
    
    header("Location: ../catalogo_personal.php");
    exit();
}

// Función para verificar si una tabla existe
function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}
?>