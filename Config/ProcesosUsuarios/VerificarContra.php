<?php
Include_once "../../templates/Sesion.php";
  $VERSION= getenv('VERSION');
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['contrasenia']) && !empty($_POST['contrasenia'])) {
        $contraseniaIngresada = $_POST['contrasenia'];
        
        $usuarioActual = $_SESSION['idusuario'.$VERSION] ?? null;
        
        if ($usuarioActual) {
            $sentencia = $Conexion->prepare("SELECT Contrasenia FROM t_usuario WHERE IdUsuario = ?");
            $sentencia->execute([$usuarioActual]);
            $usuario = $sentencia->fetch(PDO::FETCH_OBJ);
            
            if ($usuario && password_verify($contraseniaIngresada, $usuario->Contrasenia)) {
                $response['success'] = true;
            }
        }
    }
}

echo json_encode($response);
?>