<?php
Include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $IdPersonal = $_POST['IdPersonal'];
        
        // Recibir datos
        $nombre = trim($_POST['nombre']);
        $ap_paterno = trim($_POST['ap_paterno']);
        $ap_materno = trim($_POST['ap_materno']);
        $empresa = $_POST['empresa'];
        $cargo = $_POST['cargo'];
        $departamento = $_POST['departamento'];
        $ubicacion = $_POST['ubicacion'];
        $status = $_POST['status'];
        $foto_actual = $_POST['foto_actual'];
        
        // Manejo de la nueva foto
        $rutaFoto = $foto_actual;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $directorio = "../../uploads/fotos_personal/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $nombreArchivo = time() . '_' . basename($_FILES['foto']['name']);
            $rutaCompleta = $directorio . $nombreArchivo;
            
            // Validar tipo de archivo
            $extension = strtolower(pathinfo($rutaCompleta, PATHINFO_EXTENSION));
            $extensionesPermitidas = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($extension, $extensionesPermitidas)) {
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
                    // Eliminar foto anterior si existe
                    if (!empty($foto_actual) && file_exists("../../" . $foto_actual)) {
                        unlink("../../" . $foto_actual);
                    }
                    $rutaFoto = "uploads/fotos_personal/" . $nombreArchivo;
                }
            }
        }
        
        // Actualizar en la base de datos
        $sql = "UPDATE t_personal SET 
                Nombre = ?, 
                ApPaterno = ?, 
                ApMaterno = ?, 
                Empresa = ?, 
                Cargo = ?, 
                Departamento = ?, 
                IdUbicacion = ?, 
                RutaFoto = ?, 
                Status = ?,
                FechaModificacion = NOW()
                WHERE IdPersonal = ?";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->execute([$nombre, $ap_paterno, $ap_materno, $empresa, $cargo, $departamento, $ubicacion, $rutaFoto, $status, $IdPersonal]);
        
        $_SESSION['success'] = "Personal actualizado correctamente";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar personal: " . $e->getMessage();
    }
    
    header("Location: ../catalogo_personal.php");
    exit();
}
?>