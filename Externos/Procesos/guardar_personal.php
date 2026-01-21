<?php
// Controlador/Guardar_PersonalExterno.php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8");

$response = ['success' => false, 'message' => ''];

// Obtener datos del formulario
$numeroIdentificacion = $_POST['NumeroIdentificacion'] ?? '';
$nombre = $_POST['Nombre'] ?? '';
$apPaterno = $_POST['ApPaterno'] ?? '';
$apMaterno = $_POST['ApMaterno'] ?? '';
$empresa = $_POST['EmpresaProcedencia'] ?? '';
$cargo = $_POST['Cargo'] ?? '';
$areaVisita = $_POST['AreaVisita'] ?? '';
$idPersonalResponsable = $_POST['IdPersonalResponsable'] ?? null;
$email = $_POST['Email'] ?? '';
$telefono = $_POST['Telefono'] ?? '';
$status = $_POST['Status'] ?? '1';
$vigenciaAcceso = $_POST['VigenciaAcceso'] ?? null;

// Validar datos requeridos
if (empty($numeroIdentificacion) || empty($nombre) || empty($apPaterno)) {
    $response['message'] = 'Los campos No. Identificación, Nombre y Apellido Paterno son requeridos';
    echo json_encode($response);
    exit;
}

// Manejar subida de foto
$rutaFoto = '';
if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] == 0) {
    $directorio = '../imagenes/personal_externo/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    
    $nombreArchivo = time() . '_' . basename($_FILES['Foto']['name']);
    $rutaCompleta = $directorio . $nombreArchivo;
    
    if (move_uploaded_file($_FILES['Foto']['tmp_name'], $rutaCompleta)) {
        $rutaFoto = str_replace('../', '', $rutaCompleta);
    }
}

// Insertar en la base de datos
$sql = "INSERT INTO PersonalExterno (
            NumeroIdentificacion, Nombre, ApPaterno, ApMaterno, 
            EmpresaProcedencia, Cargo, AreaVisita, IdPersonalResponsable,
            Email, Telefono, Status, FechaRegistro, VigenciaAcceso, RutaFoto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
    
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param(
        'sssssssisssss',
        $numeroIdentificacion, $nombre, $apPaterno, $apMaterno,
        $empresa, $cargo, $areaVisita, $idPersonalResponsable,
        $email, $telefono, $status, $vigenciaAcceso, $rutaFoto
    );
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Personal externo registrado correctamente';
    } else {
        $response['message'] = 'Error al registrar: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['message'] = 'Error en la consulta: ' . $conn->error;
}

$conn->close();
echo json_encode($response);
?>