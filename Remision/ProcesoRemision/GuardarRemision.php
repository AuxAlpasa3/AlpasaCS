<?php
include_once "../../templates/Sesion.php";
header('Content-Type: application/json');

try {
    $mov = $_POST['Mov'] ?? '';
    $response = ['success' => false, 'message' => ''];

    switch ($mov) {
        case 'AgregarRemision':
            $IdRemision = $_POST['IdRemision'];
            $Almacen = $_POST['Almacen'];
            $TipoRemision = $_POST['TipoRemision'];
            $Fecha = $_POST['Fecha'];
            $Cliente = $_POST['Cliente'];
            $Transportista = $_POST['Transportista'] ?? '';
            $Placas = $_POST['Placas'] ?? '';
            $Chofer = $_POST['Chofer'] ?? '';
            $Contenedor = $_POST['Contenedor'] ?? '';
            $Caja = $_POST['Caja'] ?? '';
            $Tracto = $_POST['Tracto'] ?? '';
            $Sellos = $_POST['Sellos'] ?? '';
            $IdUsuario = $_POST['IdUsuario'];
            $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];

            $materiales = [];
            if (isset($_POST['MaterialNo']) && is_array($_POST['MaterialNo'])) {
                foreach ($_POST['MaterialNo'] as $index => $idArticulo) {
                    $materiales[] = [
                        'IdLinea' => $_POST['IdLinea'][$index] ?? ($index + 1),
                        'Articulo' => $idArticulo,
                        'Piezas' => $_POST['Piezas'][$index] ?? 0,
                        'Booking' => $_POST['Booking'][$index] ?? '',
                        'Cantidad' => $_POST['Cantidad'][$index] ?? 1
                    ];
                }
            }

            if (empty($materiales)) {
                throw new Exception("Debe agregar al menos un material");
            }

            $cantidadTotal = 0;
            foreach ($materiales as $material) {
                $cantidadTotal += intval($material['Cantidad']);
            }

            $estatus = 0; 
            $sqlCheckEncabezado = "SELECT COUNT(*) as count FROM t_remision_Encabezado 
                                 WHERE IdRemision = ?";
            $stmtCheckEncabezado = $Conexion->prepare($sqlCheckEncabezado);
            $stmtCheckEncabezado->execute([$IdRemision]);
            $existeEncabezado = $stmtCheckEncabezado->fetch(PDO::FETCH_OBJ)->count > 0;

            if (!$existeEncabezado) {
                $sqlEncabezado = "INSERT INTO t_remision_Encabezado (
                     IdRemision, Cliente, Transportista, Placas, Chofer, 
                    FechaRemision, TipoRemision, Cantidad, FechaRegistro, Estatus, 
                    Supervisor, Almacen, Contenedor, Sellos, Tracto, Caja
                ) VALUES (?,?,?,?,?,?,?,?,GETDATE(),?,?,?,?,?,?,?)";
                
                $stmtEncabezado = $Conexion->prepare($sqlEncabezado);
                $stmtEncabezado->execute([
                    $IdRemision,
                    $Cliente,
                    $Transportista,
                    $Placas,
                    $Chofer,
                    $Fecha,
                    $TipoRemision,
                    $cantidadTotal, 
                    $estatus,
                    $IdUsuario,
                    $Almacen,
                    $Contenedor,
                    $Sellos,
                    $Tracto,
                    $Caja
                ]);

                $IdRemisionEncabezadoInsertado = $IdRemisionEncabezado;
            } else {
                $sqlGetId = "SELECT IdRemisionEncabezado FROM t_remision_Encabezado 
                            WHERE IdRemision = ?";
                $stmtGetId = $Conexion->prepare($sqlGetId);
                $stmtGetId->execute([$IdRemision]);
                $encabezadoExistente = $stmtGetId->fetch(PDO::FETCH_OBJ);
                $IdRemisionEncabezadoInsertado = $encabezadoExistente->IdRemisionEncabezado;
            }

            $lineaCounter = 1; 
            foreach ($materiales as $material) {
                $cantidadMaterial = intval($material['Cantidad']);
                
                for ($i = 0; $i < $cantidadMaterial; $i++) {
                    $sqlLinea = "INSERT INTO t_remision_linea (
                        IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef,Booking
                    ) VALUES (?, ?, ?, ?, ?, ?, ?,?)";
                    
                    $stmtLinea = $Conexion->prepare($sqlLinea);
                    $stmtLinea->execute([
                        $IdRemision,
                        $lineaCounter, 
                        $material['Articulo'],
                        $material['Piezas'],
                        $Cliente,
                        $Almacen,
                        $IdRemisionEncabezadoInsertado,
                        $material['Booking'],
                    ]);
                    
                    $lineaCounter++; 
                }
            }

            $response['success'] = true;
            $response['message'] = 'Remisión guardada exitosamente';
            $response['data'] = [
                'idRemision' => $IdRemision,
                'idRemisionEncabezado' => $IdRemisionEncabezadoInsertado
            ];
            break;
            $IdRemision = $_POST['IdRemision'];
            $materiales = json_decode($_POST['Materiales'], true);

            // Obtener información de la remisión
            $sqlGetInfo = "SELECT IdRemisionEncabezado, Cliente, Almacen 
                          FROM t_remision_Encabezado 
                          WHERE IdRemision = ?";
            $stmtGetInfo = $Conexion->prepare($sqlGetInfo);
            $stmtGetInfo->execute([$IdRemision]);
            $infoRemision = $stmtGetInfo->fetch(PDO::FETCH_OBJ);

            if (!$infoRemision) {
                throw new Exception("No se encontró información para la remisión: " . $IdRemision);
            }

            $IdRemisionEncabezado = $infoRemision->IdRemisionEncabezado;

            // Guardar materiales si se proporcionan - TAMBIÉN REPETIR según Cantidad
            if (!empty($materiales)) {
                // Obtener el máximo IdLinea actual para continuar desde ahí
                $sqlMaxLinea = "SELECT MAX(IdLinea) as maxLinea FROM t_remision_linea WHERE IdRemision = ?";
                $stmtMaxLinea = $Conexion->prepare($sqlMaxLinea);
                $stmtMaxLinea->execute([$IdRemision]);
                $maxLinea = $stmtMaxLinea->fetch(PDO::FETCH_OBJ)->maxLinea ?? 0;
                $lineaCounter = $maxLinea + 1;

                foreach ($materiales as $material) {
                    $cantidadMaterial = intval($material['Cantidad'] ?? 1);
                    
                    // Insertar el mismo registro N veces según la cantidad
                    for ($i = 0; $i < $cantidadMaterial; $i++) {
                        // Verificar si el material ya existe (mismo IdRemision, IdLinea e IdArticulo)
                        $sqlCheck = "SELECT COUNT(*) as count FROM t_remision_linea 
                                    WHERE IdRemision = ? AND IdLinea = ? AND IdArticulo = ?";
                        $stmtCheck = $Conexion->prepare($sqlCheck);
                        $stmtCheck->execute([$IdRemision, $lineaCounter, $material['Articulo']]);
                        $exists = $stmtCheck->fetch(PDO::FETCH_OBJ)->count > 0;

                        if (!$exists) {
                            $sql = "INSERT INTO t_remision_linea (
                                IdRemision, IdLinea, IdArticulo, Piezas, Cliente, Almacen, IdRemisionEncabezadoRef
                            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            
                            $stmt = $Conexion->prepare($sql);
                            $stmt->execute([
                                $IdRemision,
                                $lineaCounter,
                                $material['Articulo'],
                                $material['Piezas'],
                                $infoRemision->Cliente,
                                $infoRemision->Almacen,
                                $IdRemisionEncabezado
                            ]);
                        }
                        
                        $lineaCounter++; // Incrementar contador para la siguiente línea
                    }
                }
            }

            $response['success'] = true;
            $response['message'] = 'Remisión finalizada correctamente';
            break;

        default:
            $response['message'] = 'Movimiento no reconocido: ' . $mov;
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log("Error en procesar_remision.php: " . $e->getMessage());
}

echo json_encode($response);
?>