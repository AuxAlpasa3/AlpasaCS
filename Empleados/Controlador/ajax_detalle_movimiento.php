<?php
include '../../api/db/conexion.php';

if (!isset($_GET['idMov']) || !isset($_GET['tipo'])) {
    die('Parámetros inválidos');
}

$tipo = $_GET['tipo'] ?? '';
$idMov = $_GET['idMov'] ?? 0;
$categoria = $_GET['categoria'] ?? ($tipo == 'entrada' ? 1 : 2); // Valor por defecto

if (empty($tipo) || empty($idMov)) {
    die('<div class="alert alert-danger">Parámetros inválidos</div>');
}

try {
    // Consulta principal del movimiento
    if ($tipo == 'entrada') {
        $sql = "SELECT * FROM regentper WHERE FolMov = :idMov";
    } else {
        $sql = "SELECT * FROM regsalper WHERE FolMov = :idMov";
    }
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':idMov', $idMov, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetch(PDO::FETCH_OBJ);
    
    // Consulta para obtener las fotografías
    $sqlFotosEnc = "SELECT idfotografias FROM t_fotografias_encabezado 
                    WHERE tipo = :categoria AND idEntSal = :idMov";
    $stmtFotosEnc = $Conexion->prepare($sqlFotosEnc);
    $stmtFotosEnc->bindParam(':categoria', $categoria, PDO::PARAM_INT);
    $stmtFotosEnc->bindParam(':idMov', $idMov, PDO::PARAM_INT);
    $stmtFotosEnc->execute();
    $encabezadoFotos = $stmtFotosEnc->fetch(PDO::FETCH_OBJ);
    
    $fotografias = [];
    
    if ($encabezadoFotos) {
        // Consulta detalle de fotografías
        $sqlFotosDet = "SELECT NombreFoto, RutaFot 
                        FROM t_fotografias_detalle 
                        WHERE idfotografiaref = :idFotografiaRef";
        $stmtFotosDet = $Conexion->prepare($sqlFotosDet);
        $stmtFotosDet->bindParam(':idFotografiaRef', $encabezadoFotos->idfotografias, PDO::PARAM_INT);
        $stmtFotosDet->execute();
        $fotografias = $stmtFotosDet->fetchAll(PDO::FETCH_OBJ);
    }
    
} catch (PDOException $e) {
    die('<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>');
}
?>

<!-- Modal Único con toda la información -->
<div class="modal fade" id="modalDetalleUnico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> 
                    Detalle de <?php echo $tipo == 'entrada' ? 'Entrada' : 'Salida'; ?>
                    <span class="badge badge-light ml-2">ID: <?php echo $idMov; ?></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Tabla de Información del Movimiento -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-clipboard-list mr-2"></i>Información General
                    </h6>
                    
                    <?php if ($detalle): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="40%">Campo</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalle as $campo => $valor): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($campo); ?></strong></td>
                                            <td><?php echo htmlspecialchars($valor ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>No se encontraron detalles para este movimiento.
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Tabla de Fotografías (si existen) -->
                <?php if (!empty($fotografias)): ?>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-camera mr-2"></i>Fotografías Adjuntas
                        <span class="badge badge-primary badge-pill ml-2"><?php echo count($fotografias); ?></span>
                    </h6>
                    
                    <!-- Tabla de 5 imágenes por fila -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <?php for ($col = 1; $col <= 5; $col++): ?>
                                        <th style="width: 20%;" class="py-1">
                                            <small>Imagen <?php echo $col; ?></small>
                                        </th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dividir fotografías en filas de 5
                                $filas_fotos = array_chunk($fotografias, 5);
                                
                                foreach ($filas_fotos as $fila): 
                                ?>
                                    <tr>
                                        <?php 
                                        // Mostrar 5 imágenes en la fila
                                        for ($i = 0; $i < 5; $i++): 
                                            if (isset($fila[$i])) {
                                                $foto = $fila[$i];
                                            } else {
                                                $foto = null;
                                            }
                                        ?>
                                            <td class="align-middle p-2" style="height: 180px;">
                                                <?php if ($foto): ?>
                                                    <?php
                                                    // Determinar ruta completa de la imagen
                                                    $rutaCompleta = $foto->RutaFot;
                                                    // Asegurarse de que la ruta sea relativa al sitio
                                                    if (strpos($rutaCompleta, 'http') !== 0) {
                                                        $rutaCompleta = ($rutaCompleta[0] != '/') ? '/' . $rutaCompleta : $rutaCompleta;
                                                    }
                                                    ?>
                                                    <div class="foto-item h-100 d-flex flex-column justify-content-between">
                                                        <!-- Vista previa de imagen -->
                                                        <div class="foto-preview flex-grow-1 d-flex align-items-center justify-content-center mb-1"
                                                             style="background-color: #f8f9fa; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;"
                                                             onclick="ampliarImagen('<?php echo htmlspecialchars($rutaCompleta); ?>', '<?php echo htmlspecialchars($foto->NombreFoto); ?>')"
                                                             title="Click para ampliar">
                                                            <img src="<?php echo htmlspecialchars($rutaCompleta); ?>" 
                                                                 class="img-fluid" 
                                                                 alt="<?php echo htmlspecialchars($foto->NombreFoto); ?>"
                                                                 style="max-height: 100px; max-width: 100%; object-fit: contain;">
                                                        </div>
                                                        
                                                        <!-- Información y acciones -->
                                                        <div class="foto-details">
                                                            <!-- Nombre de archivo -->
                                                            <small class="text-muted d-block text-truncate mb-1" 
                                                                   title="<?php echo htmlspecialchars($foto->NombreFoto); ?>">
                                                                <i class="fas fa-file-image fa-xs mr-1"></i>
                                                                <?php echo htmlspecialchars(substr($foto->NombreFoto, 0, 15)); ?>
                                                                <?php if (strlen($foto->NombreFoto) > 15): ?>...<?php endif; ?>
                                                            </small>
                                                            
                                                            <!-- Botones de acción -->
                                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                                <a href="<?php echo htmlspecialchars($rutaCompleta); ?>" 
                                                                   target="_blank" 
                                                                   class="btn btn-outline-primary btn-sm flex-fill"
                                                                   title="Abrir en nueva pestaña">
                                                                    <i class="fas fa-external-link-alt fa-xs"></i>
                                                                </a>
                                                                <button type="button" 
                                                                        class="btn btn-outline-success btn-sm flex-fill"
                                                                        onclick="descargarImagen('<?php echo htmlspecialchars($rutaCompleta); ?>', '<?php echo htmlspecialchars($foto->NombreFoto); ?>')"
                                                                        title="Descargar imagen">
                                                                    <i class="fas fa-download fa-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Celda vacía -->
                                                    <div class="celda-vacia h-100 d-flex align-items-center justify-content-center"
                                                         style="background-color: #f8f9fa; border-radius: 4px; border: 1px dashed #ddd;">
                                                        <small class="text-muted">
                                                            <i class="fas fa-ban"></i> Vacío
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="py-1">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Mostrando <?php echo count($fotografias); ?> imágenes en <?php echo count($filas_fotos); ?> fila(s)
                                        </small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="mt-4 pt-3 border-top">
                    <div class="alert alert-info py-2 mb-0">
                        <i class="fas fa-info-circle mr-2"></i> 
                        No hay fotografías registradas para este movimiento.
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar el modal y las imágenes -->
<script>
$(document).ready(function() {
    // Mostrar el modal único
    $('#modalDetalleUnico').modal('show');
    
    // Configurar cierre del modal
    $('#modalDetalleUnico').on('hidden.bs.modal', function () {
        // Limpiar si es necesario
    });
});

// Función para ampliar imágenes (sin modal adicional)
function ampliarImagen(ruta, titulo) {
    // Crear overlay para vista ampliada
    var overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    `;
    
    // Crear contenedor de imagen
    var imgContainer = document.createElement('div');
    imgContainer.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        position: relative;
    `;
    
    // Crear imagen ampliada
    var img = document.createElement('img');
    img.src = ruta;
    img.alt = titulo || 'Imagen ampliada';
    img.style.cssText = `
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 80vh;
        border-radius: 5px;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
    `;
    
    // Crear título
    var titleDiv = document.createElement('div');
    titleDiv.style.cssText = `
        color: white;
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
        padding: 5px 10px;
        background: rgba(0,0,0,0.7);
        border-radius: 3px;
    `;
    titleDiv.textContent = titulo || 'Imagen';
    
    // Crear botones de acción
    var actionsDiv = document.createElement('div');
    actionsDiv.style.cssText = `
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    `;
    
    // Botón para abrir en nueva pestaña
    var openBtn = document.createElement('button');
    openBtn.innerHTML = '<i class="fas fa-external-link-alt mr-1"></i> Abrir';
    openBtn.style.cssText = `
        background: #007bff;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    `;
    openBtn.onclick = function(e) {
        e.stopPropagation();
        window.open(ruta, '_blank');
    };
    
    // Botón para descargar
    var downloadBtn = document.createElement('button');
    downloadBtn.innerHTML = '<i class="fas fa-download mr-1"></i> Descargar';
    downloadBtn.style.cssText = `
        background: #28a745;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    `;
    downloadBtn.onclick = function(e) {
        e.stopPropagation();
        descargarImagen(ruta, titulo);
    };
    
    // Botón para cerrar
    var closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Cerrar';
    closeBtn.style.cssText = `
        background: #6c757d;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    `;
    closeBtn.onclick = function(e) {
        e.stopPropagation();
        document.body.removeChild(overlay);
    };
    
    // Ensamblar todo
    actionsDiv.appendChild(openBtn);
    actionsDiv.appendChild(downloadBtn);
    actionsDiv.appendChild(closeBtn);
    imgContainer.appendChild(img);
    imgContainer.appendChild(titleDiv);
    imgContainer.appendChild(actionsDiv);
    overlay.appendChild(imgContainer);
    
    // Cerrar al hacer click fuera de la imagen
    overlay.onclick = function(e) {
        if (e.target === overlay) {
            document.body.removeChild(overlay);
        }
    };
    
    // Cerrar con tecla ESC
    var keyHandler = function(e) {
        if (e.key === 'Escape') {
            document.body.removeChild(overlay);
            document.removeEventListener('keydown', keyHandler);
        }
    };
    document.addEventListener('keydown', keyHandler);
    
    // Agregar al documento
    document.body.appendChild(overlay);
}

// Función para descargar imágenes
function descargarImagen(ruta, nombre) {
    var enlace = document.createElement('a');
    enlace.href = ruta;
    enlace.download = nombre || 'imagen.jpg';
    document.body.appendChild(enlace);
    enlace.click();
    document.body.removeChild(enlace);
}

// Efecto hover en las imágenes
$(document).on('mouseenter', '.foto-preview', function() {
    $(this).css({
        'border-color': '#007bff',
        'box-shadow': '0 2px 5px rgba(0,123,255,0.3)'
    });
}).on('mouseleave', '.foto-preview', function() {
    $(this).css({
        'border-color': '#ddd',
        'box-shadow': 'none'
    });
});
</script>

<style>
/* Estilos para el modal único */
#modalDetalleUnico .modal-body {
    padding: 15px;
}

/* Estilos para las tablas */
.table-sm th, .table-sm td {
    padding: 8px 10px;
    font-size: 13px;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Estilos para las celdas de imágenes */
.foto-item {
    transition: all 0.2s ease;
}

.foto-preview img {
    transition: transform 0.3s ease;
}

.foto-preview:hover img {
    transform: scale(1.05);
}

.celda-vacia {
    opacity: 0.6;
    transition: opacity 0.2s ease;
}

.celda-vacia:hover {
    opacity: 0.8;
}

/* Botones compactos */
.btn-group-sm > .btn {
    padding: 2px 5px;
    font-size: 11px;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    
    #modalDetalleUnico .modal-body {
        max-height: 60vh;
    }
    
    .table-responsive {
        margin-bottom: 10px;
    }
    
    .foto-item {
        font-size: 11px;
    }
    
    .foto-preview {
        min-height: 80px !important;
    }
    
    .foto-preview img {
        max-height: 70px !important;
    }
}
</style>