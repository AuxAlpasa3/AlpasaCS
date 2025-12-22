<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
Include '../api/db/conexion.php';

$usuario = $_GET['usuario'];
$password = $_GET['password'];

// Validar que se recibieron los parámetros
if (empty($usuario) || empty($password)) {
    echo json_encode("Usuario y contraseña son requeridos", JSON_UNESCAPED_UNICODE);
    exit;
}

$sentencia = $Conexion->prepare("SELECT * FROM t_usuario where Usuario= ? and TipoUsuario in (1,2,3,4,5) and Estatus=1"); 
$sentencia->execute([$usuario]);
$client = $sentencia->fetchAll(PDO::FETCH_OBJ);

$rowCount = $sentencia->rowCount();

if($rowCount > 0) {
    $datos = array();
    
    foreach ($client as $row) {
        if (password_verify($password, $row->Contrasenia)) {
            $sentenciaAlmacenes = $Conexion->prepare("
                SELECT t3.IdAlmacen, CONCAT(t3.Almacen,' -',t4.TipoAlmacen) as Almacen, t3.NumRecinto
                FROM t_usuario as t1
                INNER JOIN t_usuario_almacen as t2 on t1.IdUsuario=t2.IdUsuario
                INNER JOIN t_almacen t3 ON t2.IdAlmacen = t3.IdAlmacen
                INNER JOIN t_tipoAlmacen t4 on t3.Tipo=t4.IdTipoAlmacen
                WHERE t1.IdUsuario = ? AND t1.Estatus = 1");
            $sentenciaAlmacenes->execute([$row->IdUsuario]);
            $almacenes = $sentenciaAlmacenes->fetchAll(PDO::FETCH_OBJ);
            
            $almacenesArray = array();
            foreach ($almacenes as $almacen) {
                $almacenesArray[] = array(
                    'IdAlmacen' => $almacen->IdAlmacen,
                    'Almacen' => $almacen->Almacen,
                    'NumRecinto' => $almacen->NumRecinto
                );
            }
            
            $ultimoAlmacen = $row->Sesion;
            $almacenSeleccionado = null;
            $idAlmacenSeleccionado = null;
            $numRecintoSeleccionado = null;
            
            // Si solo tiene un almacén, seleccionarlo automáticamente
            if (count($almacenesArray) == 1) {
                $almacenSeleccionado = $almacenesArray[0]['Almacen'];
                $idAlmacenSeleccionado = $almacenesArray[0]['IdAlmacen'];
                $numRecintoSeleccionado = $almacenesArray[0]['NumRecinto'];
            } 
            // Si tiene múltiples almacenes y hay un último almacén usado
            else if (count($almacenesArray) > 1 && !empty($ultimoAlmacen)) {
                foreach ($almacenesArray as $almacen) {
                    if ($almacen['IdAlmacen'] == $ultimoAlmacen) {
                        $almacenSeleccionado = $almacen['Almacen'];
                        $idAlmacenSeleccionado = $almacen['IdAlmacen'];
                        $numRecintoSeleccionado = $almacen['NumRecinto'];
                        break;
                    }
                }
            }
            
            array_push($datos, array(
                'IdUsuario' => $row->IdUsuario,
                'Usuario' => $row->Usuario,
                'TipoUsuario' => $row->TipoUsuario,
                'Correo' => $row->Correo,
                'NombreColaborador' => $row->NombreColaborador,
                'Almacen' => $almacenSeleccionado,
                'IdAlmacen' => $idAlmacenSeleccionado,
                'NumRecinto' => $numRecintoSeleccionado,
                'Almacenes' => $almacenesArray
            ));
        } else {
            $datos = "Usuario o Contraseña Incorrecto, Favor de validar la información";
        }
    }
} else {
    $datos = "El Usuario no existe, Favor de validar con el Administrador";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);
?>