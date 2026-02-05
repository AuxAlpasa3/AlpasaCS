<?php
Include_once "../../templates/Sesion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $nombre = trim($_POST['nombre']);
        $ap_paterno = trim($_POST['ap_paterno']);
        $ap_materno = trim($_POST['ap_materno']);
        $empresa = $_POST['empresa'];
        $cargo = $_POST['cargo'];
        $departamento = $_POST['departamento'];
        $ubicacion = $_POST['ubicacion'];
        $status = $_POST['status'];
        
        // Manejo de la foto
        $rutaFoto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $directorio = "../fotos_personal/";
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
                    $rutaFoto = "uploads/fotos_personal/" . $nombreArchivo;
                }
            }
        }
        
        // Insertar en la base de datos
        $sql = "INSERT INTO t_personal (Nombre, ApPaterno, ApMaterno, Empresa, Cargo, Departamento, IdUbicacion, RutaFoto, Status, FechaCreacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->execute([$nombre, $ap_paterno, $ap_materno, $empresa, $cargo, $departamento, $ubicacion, $rutaFoto, $status]);
        
        $_SESSION['success'] = "Personal registrado correctamente";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar personal: " . $e->getMessage();
    }
    
    header("Location: ../catalogo_personal.php");
    exit();
}
?>