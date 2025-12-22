<?php
session_start();
include_once "../../config/conexion.php";

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

// Verificar token CSRF
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['success' => false, 'message' => 'Token de seguridad inválido']));
}

try {
    // Validar datos recibidos
    $IdPersonal = filter_var($_POST['IdPersonal'] ?? 0, FILTER_VALIDATE_INT);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmacion = trim($_POST['confirmacion'] ?? '');
    $motivo = trim($_POST['motivo_eliminacion'] ?? '');
    
    // Validaciones básicas
    if ($IdPersonal <= 0) {
        throw new Exception('ID de personal inválido');
    }
    
    if (empty($username)) {
        throw new Exception('Usuario no especificado');
    }
    
    if (empty($password)) {
        throw new Exception('Contraseña requerida');
    }
    
    if (strtoupper($confirmacion) !== 'ELIMINAR PERMANENTEMENTE') {
        throw new Exception('Confirmación incorrecta');
    }
    
    if (strlen($motivo) < 10) {
        throw new Exception('El motivo debe tener al menos 10 caracteres');
    }
    
    // Obtener información del usuario actual
    $sqlUsuario = "SELECT IdUsuario, Username, Password, NivelAcceso FROM t_usuarios WHERE Username = ? AND Status = 1";
    $stmtUsuario = $Conexion->prepare($sqlUsuario);
    $stmtUsuario->execute([$username]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_OBJ);
    
    if (!$usuario) {
        throw new Exception('Usuario no encontrado o inactivo');
    }
    
    // Verificar contraseña (ajustar según tu sistema de hash)
    // Si usas password_hash():
    if (!password_verify($password, $usuario->Password)) {
        throw new Exception('Contraseña incorrecta');
    }
    
    // Verificar permisos del usuario
    if ($usuario->NivelAcceso < 2) { // Ajustar según tus niveles de acceso
        throw new Exception('No tiene permisos suficientes para eliminar registros');
    }
    
    // Obtener información completa del personal antes de eliminar
    $sqlInfoPersonal = "SELECT 
        p.*,
        e.NomEmpresa,
        c.NomCargo,
        d.NomDepto,
        u.NomCorto as Ubicacion
    FROM t_personal p
    LEFT JOIN t_empresa e ON p.Empresa = e.IdEmpresa
    LEFT JOIN t_cargo c ON p.Cargo = c.IdCargo
    LEFT JOIN t_departamento d ON p.Departamento = d.IdDepartamento
    LEFT JOIN t_ubicacion u ON p.IdUbicacion = u.IdUbicacion
    WHERE p.IdPersonal = ?";
    
    $stmtInfo = $Conexion->prepare($sqlInfoPersonal);
    $stmtInfo->execute([$IdPersonal]);
    $personalInfo = $stmtInfo->fetch(PDO::FETCH_OBJ);
    
    if (!$personalInfo) {
        throw new Exception('Registro de personal no encontrado');
    }
    
    // Iniciar transacción
    $Conexion->beginTransaction();
    
    // 1. Registrar en tabla de eliminados (backup)
    $sqlBackup = "INSERT INTO t_personal_eliminados (
        IdPersonalOriginal,
        Nombre,
        ApPaterno,
        ApMaterno,
        Empresa,
        Cargo,
        Departamento,
        IdUbicacion,
        RutaFoto,
        Status,
        FechaCreacion,
        FechaEliminacion,
        UsuarioElimino,
        MotivoEliminacion,
        DatosCompletos
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    
    $datosCompletos = json_encode($personalInfo, JSON_UNESCAPED_UNICODE);
    
    $stmtBackup = $Conexion->prepare($sqlBackup);
    $stmtBackup->execute([
        $personalInfo->IdPersonal,
        $personalInfo->Nombre,
        $personalInfo->ApPaterno,
        $personalInfo->ApMaterno,
        $personalInfo->Empresa,
        $personalInfo->Cargo,
        $personalInfo->Departamento,
        $personalInfo->IdUbicacion,
        $personalInfo->RutaFoto,
        $personalInfo->Status,
        $personalInfo->FechaCreacion,
        $usuario->IdUsuario,
        $motivo,
        $datosCompletos
    ]);
    
    // 2. Eliminar foto si existe
    if (!empty($personalInfo->RutaFoto) && file_exists("../../" . $personalInfo->RutaFoto)) {
        unlink("../../" . $personalInfo->RutaFoto);
    }
    
    // 3. Eliminar registro principal
    $sqlEliminar = "DELETE FROM t_personal WHERE IdPersonal = ?";
    $stmtEliminar = $Conexion->prepare($sqlEliminar);
    $stmtEliminar->execute([$IdPersonal]);
    
    // 4. Registrar en log de actividades
    $sqlLog = "INSERT INTO t_log_actividades (
        IdUsuario,
        Accion,
        TablaAfectada,
        IdRegistroAfectado,
        Descripcion,
        DireccionIP,
        UserAgent
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $accion = "ELIMINACIÓN PERMANENTE DE PERSONAL";
    $descripcion = "Se eliminó permanentemente al personal: " . 
                   $personalInfo->Nombre . " " . 
                   $personalInfo->ApPaterno . " " . 
                   $personalInfo->ApMaterno . 
                   " | Motivo: " . $motivo;
    
    $stmtLog = $Conexion->prepare($sqlLog);
    $stmtLog->execute([
        $usuario->IdUsuario,
        $accion,
        't_personal',
        $IdPersonal,
        $descripcion,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);
    
    // Confirmar transacción
    $Conexion->commit();
    
    // Crear tabla de eliminados si no existe
    crearTablaEliminados($Conexion);
    crearTablaLog($Conexion);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registro eliminado permanentemente. Se ha creado un backup en el historial.'
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }
    
    error_log("Error eliminando personal ID $IdPersonal: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// Funciones auxiliares
function crearTablaEliminados($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS t_personal_eliminados (
        IdBackup INT AUTO_INCREMENT PRIMARY KEY,
        IdPersonalOriginal INT NOT NULL,
        Nombre VARCHAR(100) NOT NULL,
        ApPaterno VARCHAR(100) NOT NULL,
        ApMaterno VARCHAR(100) NOT NULL,
        Empresa INT DEFAULT 0,
        Cargo INT DEFAULT 0,
        Departamento INT DEFAULT 0,
        IdUbicacion INT DEFAULT 0,
        RutaFoto VARCHAR(255),
        Status TINYINT(1) DEFAULT 1,
        FechaCreacion DATETIME,
        FechaEliminacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        UsuarioElimino INT,
        MotivoEliminacion TEXT,
        DatosCompletos JSON,
        INDEX idx_fecha_eliminacion (FechaEliminacion),
        INDEX idx_usuario_elimino (UsuarioElimino)
    )";
    
    $pdo->exec($sql);
}

function crearTablaLog($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS t_log_actividades (
        IdLog INT AUTO_INCREMENT PRIMARY KEY,
        IdUsuario INT,
        Accion VARCHAR(100) NOT NULL,
        TablaAfectada VARCHAR(50),
        IdRegistroAfectado INT,
        Descripcion TEXT,
        DireccionIP VARCHAR(45),
        UserAgent TEXT,
        FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_fecha (FechaRegistro),
        INDEX idx_usuario (IdUsuario),
        INDEX idx_accion (Accion)
    )";
    
    $pdo->exec($sql);
}
?>