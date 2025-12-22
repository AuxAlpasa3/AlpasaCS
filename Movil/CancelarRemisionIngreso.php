<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    $input = $_POST;
}

if(isset($input['Mov']))
{
    switch($input['Mov'])
    {
        case 'Cancelar':
            Cancelar($input);
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Movimiento no válido'
            ]);
            break;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se especificó el movimiento'
    ]);
}

function Cancelar($data) 
{   
    try 
    {
        // Validar datos requeridos
        $requiredFields = ['IdTarja', 'TipoRemision', 'IdAlmacen', 'user', 'confirmed_password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => "Falta el campo requerido: $field"
                ]);
                return;
            }
        }

        $rutaServidor = getenv('DB_HOST');
        $nombreBaseDeDatos = getenv('DB');
        $usuarioDB = getenv('DB_USER');
        $contraseñaDB = getenv('DB_PASS');

        $Conexion = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuarioDB, $contraseñaDB);
        $Conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $ZonaHoraria = getenv('ZonaHoraria');
        date_default_timezone_set($ZonaHoraria);
        $fechahora = date('Ymd H:i:s');
        
        $IdTarja = $data['IdTarja'];
        $TipoRemision = $data['TipoRemision'];
        $Almacen = $data['IdAlmacen'];
        $usuarioId = $data['user'];
        $password = $data['confirmed_password'];

        // Validar que el tipo de remisión sea válido
        if (!in_array($TipoRemision, ['1', '2'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Tipo de remisión no válido'
            ]);
            return;
        }

        $stmt = $Conexion->prepare("SELECT Contrasenia FROM t_usuario WHERE IdUsuario = ?");
        $stmt->execute([$usuarioId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$user || !password_verify($password, $user['Contrasenia'])) {
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña no es válida'
            ]);
            return;
        }

        try 
        {
            if($TipoRemision == '1') // Ingresos
            {
                $stmt = $Conexion->prepare("SELECT distinct(IdRemision) as IdRemision FROM t_ingreso where IdTarja=? and Almacen=?");
                $stmt->execute([$IdTarja, $Almacen]);
                $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (empty($Remisiones)) 
                {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se encontraron datos de la tarja especificada'
                    ]);
                    return;
                }

                $Conexion->beginTransaction();

                $stmtDelete = $Conexion->prepare("DELETE FROM t_ingreso where IdTarja=? and IdRemision=? and Almacen=?");
                $stmtDeleteFoto = $Conexion->prepare("DELETE FROM t_fotografias_Encabezado WHERE IdTarja=? and Almacen=?");
                $stmtDeleteFotoDet = $Conexion->prepare("DELETE FROM t_fotografias_Detalle WHERE idfotografiaref in(Select IdFotografias from t_fotografias_Encabezado where IdTarja=? and Almacen=?)");
                
                foreach($Remisiones as $Remision) {
                    $IdRemision = $Remision->IdRemision;

                    $result = $stmtDelete->execute([$IdTarja, $IdRemision, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar el Ingreso");
                    }

                    $result = $stmtDeleteFoto->execute([$IdTarja, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar las fotografías del encabezado");
                    }

                    $result = $stmtDeleteFotoDet->execute([$IdTarja, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar las fotografías del detalle");
                    }

                    $stmtUpdateRemision = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 1 WHERE IdRemisionEncabezado = ?");
                    $result = $stmtUpdateRemision->execute([$IdRemision]);
                    
                    if (!$result) {
                        throw new Exception("Error al actualizar remisión");
                    }
                }

                $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) VALUES (?, ?, ?, ?, ?)";
                $stmt = $Conexion->prepare($consulta);
                $stmt->execute([
                    't_ingreso', 
                    'CancelarRemision'.$IdTarja, 
                    $fechahora, 
                    "Cancelar remisión", 
                    $usuarioId
                ]);

                $Conexion->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Ingreso cancelado correctamente',
                    'data' => [
                        'IdTarja' => $IdTarja,
                        'TipoRemision' => $TipoRemision,
                        'RemisionesAfectadas' => count($Remisiones)
                    ]
                ]);
            }
            elseif($TipoRemision == '2') // Salidas
            {
                $stmt = $Conexion->prepare("SELECT distinct(IdRemision) as IdRemision FROM t_Salida where IdTarja=? and Almacen=?");
                $stmt->execute([$IdTarja, $Almacen]);
                $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (empty($Remisiones)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se encontraron datos de la tarja especificada'
                    ]);
                    return;
                }

                $Conexion->beginTransaction();

                $stmtDelete = $Conexion->prepare("DELETE FROM t_Salida where IdTarja=? and IdRemision=? and Almacen=?");
                $stmtDeleteFoto = $Conexion->prepare("DELETE FROM t_fotografias_Encabezado WHERE IdTarja=? and Almacen=?");
                $stmtDeleteFotoDet = $Conexion->prepare("DELETE FROM t_fotografias_Detalle WHERE idfotografiaref in(Select IdFotografias from t_fotografias_Encabezado where IdTarja=? and Almacen=?)");
                $stmtDeleteFotoRemLinea = $Conexion->prepare("DELETE FROM t_remision_linea WHERE IdRemisionEncabezadoRef=?");
                $stmtDeletePasoSalida = $Conexion->prepare("DELETE FROM t_pasoSalida where IdRemision=?");
                
                foreach($Remisiones as $Remision) {
                    $IdRemision = $Remision->IdRemision;

                    $result = $stmtDelete->execute([$IdTarja, $IdRemision, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar la Salida");
                    }
                    
                    $result = $stmtDeleteFoto->execute([$IdTarja, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar las fotografías del encabezado");
                    }

                    $result = $stmtDeleteFotoDet->execute([$IdTarja, $Almacen]);
                    if (!$result) {
                        throw new Exception("Error al eliminar las fotografías del detalle");
                    }

                    $result = $stmtDeleteFotoRemLinea->execute([$IdRemision]);
                    if (!$result) {
                        throw new Exception("Error al eliminar las líneas de remisión");
                    }

                    $result = $stmtDeletePasoSalida->execute([$IdRemision]);
                    if (!$result) {
                        throw new Exception("Error al eliminar el paso de salida");
                    }

                    $stmtUpdateRemision = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 1 WHERE IdRemisionEncabezado = ? and Almacen=?");
                    $result = $stmtUpdateRemision->execute([$IdRemision, $Almacen]);
                    
                    if (!$result) {
                        throw new Exception("Error al actualizar remisión");
                    }
                }

                $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) VALUES (?, ?, ?, ?, ?)";
                $stmt = $Conexion->prepare($consulta);
                $stmt->execute([
                    't_Salida', 
                    'CancelarRemision'.$IdTarja, 
                    $fechahora, 
                    "Cancelar remisión", 
                    $usuarioId
                ]);

                $Conexion->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Salida cancelada correctamente',
                    'data' => [
                        'IdTarja' => $IdTarja,
                        'TipoRemision' => $TipoRemision,
                        'RemisionesAfectadas' => count($Remisiones)
                    ]
                ]);
            }

        } catch (PDOException $e) {
            if ($Conexion->inTransaction()) {
                $Conexion->rollBack();
            }
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error al cancelar: ' . $e->getMessage()
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión: ' . $e->getMessage()
        ]);
    } finally {
        $Conexion = null;
    }
}
?>