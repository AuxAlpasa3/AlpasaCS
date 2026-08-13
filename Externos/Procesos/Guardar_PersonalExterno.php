<?php
include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recibir datos del formulario
        $numeroIdentificacion = $_POST['numeroIdentificacion'];
        $nombre = $_POST['nombre'];
        $apPaterno = $_POST['ap_paterno'];
        $apMaterno = $_POST['ap_materno'] ?? '';
        $empresa = $_POST['empresa'];
        $cargo = $_POST['cargo'];
        $areaVisita = $_POST['areaVisita'];
        $personalResponsable = $_POST['personalResponsable'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $vigenciaInicio = $_POST['vigenciaInicio'];
        $vigenciaFin = $_POST['vigenciaFin'];
        $estatus = $_POST['estatus'];
        
        // Procesar foto si se subió
        $fotoPath = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = time() . '_' . $_FILES['foto']['name'];
            $rutaDestino = '../../uploads/personal_externo/' . $nombreArchivo;
            
            // Crear directorio si no existe
            if (!is_dir('../../uploads/personal_externo/')) {
                mkdir('../../uploads/personal_externo/', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                $fotoPath = $nombreArchivo;
            }
        }
        
        // Insertar en la base de datos
        $sql = "INSERT INTO t_personal_externo 
                (NumeroIdentificacion, Nombre, ApPaterno, ApMaterno, EmpresaProcedencia, 
                 Cargo, AreaVisita, PersonalResponsable, Email, Telefono, 
                 VigenciaInicio, VigenciaFin, Foto, Estatus, FechaCreacion) 
                VALUES 
                (:numeroIdentificacion, :nombre, :apPaterno, :apMaterno, :empresa,
                 :cargo, :areaVisita, :personalResponsable, :email, :telefono,
                 :vigenciaInicio, :vigenciaFin, :foto, :estatus, NOW())";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->execute([
            ':numeroIdentificacion' => $numeroIdentificacion,
            ':nombre' => $nombre,
            ':apPaterno' => $apPaterno,
            ':apMaterno' => $apMaterno,
            ':empresa' => $empresa,
            ':cargo' => $cargo,
            ':areaVisita' => $areaVisita,
            ':personalResponsable' => $personalResponsable,
            ':email' => $email,
            ':telefono' => $telefono,
            ':vigenciaInicio' => $vigenciaInicio,
            ':vigenciaFin' => $vigenciaFin,
            ':foto' => $fotoPath,
            ':estatus' => $estatus
        ]);
        
        $response = [
            'success' => true,
            'message' => 'Personal externo guardado correctamente',
            'id' => $Conexion->lastInsertId()
        ];
        
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error al guardar: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>