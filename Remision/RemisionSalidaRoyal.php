<?php
include_once "../templates/head.php";
$fecha = date('Ymd');
$fechahora = date('Ymd H:i:s');
$usuario = (!empty($_POST['user'])) ? $_POST['user'] : NULL;
$IdRemisionEncabezado = (!empty($_POST['idRemisionEncabezado'])) ? $_POST['idRemisionEncabezado'] : NULL;
$IdRemision = (!empty($_POST['idRemision'])) ? $_POST['idRemision'] : NULL;
$Almacen = (!empty($_POST['Almacen'])) ? $_POST['Almacen'] : NULL;
?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php
        include_once "../templates/nav.php";
        include_once "../templates/aside.php";
        
        // Obtener tipos de transporte disponibles
        $sentTransportes = $Conexion->prepare("SELECT IdTransporte, Transporte, Alto, Ancho, Largo FROM t_medidas_transporte ORDER BY Transporte");
        $sentTransportes->execute();
        $transportes = $sentTransportes->fetchAll(PDO::FETCH_OBJ);

        // Obtener información actual de la remisión
        $sentRemisionInfo = $Conexion->prepare("
            SELECT 
                t1.IdRemision, 
                t1.IdTransporte,
                t6.NombreCliente,
                t7.Almacen,
                t4.Alto as AltoTransporte,
                t4.Ancho as AnchoTransporte,
                t4.Largo as LargoTransporte,
                t4.Transporte,
                SUM(COALESCE(t8.Alto, 0)) as AltoTotal,
                SUM(COALESCE(t8.Ancho, 0)) as AnchoTotal,
                SUM(COALESCE(t8.Largo, 0)) as LargoTotal,
                COUNT(t2.CodBarras) as TotalCodigosBarras,
                t1.Cliente
            FROM t_remision_encabezado as t1 
            LEFT JOIN t_remision_linea as t2 ON t1.IdRemisionEncabezado = t2.IdRemisionEncabezadoRef
            LEFT JOIN t_pasoSalida as t3 ON t2.CodBarras = t3.CodBarras AND t3.IdRemision = t2.IdRemisionEncabezadoRef
            LEFT JOIN t_medidas_transporte as t4 ON t1.IdTransporte = t4.IdTransporte
            INNER JOIN t_cliente as t6 ON t1.Cliente = t6.IdCliente
            INNER JOIN t_almacen as t7 ON t1.Almacen = t7.IdAlmacen
            LEFT JOIN t_ingreso as t8 ON t2.CodBarras = t8.CodBarras
            WHERE t1.IdRemisionEncabezado = ?
            GROUP BY 
                t1.IdRemision, 
                t1.IdTransporte,
                t6.NombreCliente,
                t7.Almacen,
                t4.Alto,
                t4.Ancho,
                t4.Largo,
                t4.Transporte,
                t1.Cliente
        ");
        $sentRemisionInfo->execute([$IdRemisionEncabezado]);
        $remisionInfo = $sentRemisionInfo->fetch(PDO::FETCH_OBJ);

        // Preparar datos para JavaScript
        $transportesData = [];
        foreach ($transportes as $transporte) {
            $transportesData[$transporte->IdTransporte] = [
                'Alto' => $transporte->Alto,
                'Ancho' => $transporte->Ancho,
                'Largo' => $transporte->Largo,
                'Transporte' => $transporte->Transporte
            ];
        }

        // Calcular medidas y capacidad
        if ($remisionInfo) {
            $altoTransporteCm = $remisionInfo->AltoTransporte ? $remisionInfo->AltoTransporte * 100 : 0;
            $anchoTransporteCm = $remisionInfo->AnchoTransporte ? $remisionInfo->AnchoTransporte * 100 : 0;
            $largoTransporteCm = $remisionInfo->LargoTransporte ? $remisionInfo->LargoTransporte * 100 : 0;

            $altoProductosCm = $remisionInfo->AltoTotal ?: 0;
            $anchoProductosCm = $remisionInfo->AnchoTotal ?: 0;
            $largoProductosCm = $remisionInfo->LargoTotal ?: 0;

            $volumenTransporteCm3 = $altoTransporteCm * $anchoTransporteCm * $largoTransporteCm;
            $volumenProductosCm3 = $altoProductosCm * $anchoProductosCm * $largoProductosCm;

            $porcentajeUso = $volumenTransporteCm3 > 0 ? ($volumenProductosCm3 / $volumenTransporteCm3) * 100 : 0;

            // Determinar clase CSS y estado
            if ($porcentajeUso > 100) {
                $capacityClass = 'capacity-over';
                $capacityStatus = 'SOBRECARGADO';
                $recomendacion = 'REDUCIR CARGA';
                $mensajeEstado = '⚠️ <strong>ALERTA:</strong> El transporte está SOBRECARGADO. Por favor retire algunos productos.';
                $alertClass = 'alert-danger';
            } elseif ($porcentajeUso > 80) {
                $capacityClass = 'capacity-high';
                $capacityStatus = 'ALTA CAPACIDAD';
                $recomendacion = 'MÁXIMA CAPACIDAD';
                $mensajeEstado = '🔶 <strong>ADVERTENCIA:</strong> El transporte está cerca de su capacidad máxima.';
                $alertClass = 'alert-warning';
            } elseif ($porcentajeUso > 0) {
                $capacityClass = 'capacity-good';
                $capacityStatus = 'DENTRO DE CAPACIDAD';
                $recomendacion = 'CAPACIDAD ÓPTIMA';
                $mensajeEstado = '✅ <strong>OPTIMO:</strong> El transporte tiene capacidad disponible.';
                $alertClass = 'alert-success';
            } else {
                $capacityClass = '';
                $capacityStatus = 'SIN CARGA';
                $recomendacion = 'SIN PRODUCTOS';
                $mensajeEstado = 'ℹ️ <strong>INFORMACIÓN:</strong> No hay productos cargados en el transporte.';
                $alertClass = 'alert-info';
            }
            
            $capacidadDisponible = $volumenTransporteCm3 > 0 ? $volumenTransporteCm3 - $volumenProductosCm3 : 0;
        }
        ?>
        <style>
        #DarSalida:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #cccccc !important;
            border-color: #cccccc !important;
        }

        .card-disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .search-container {
            margin-bottom: 15px;
        }

        .info-table th {
            color: darkorange;
            text-align: center;
            padding: 8px;
        }

        .info-table td {
            text-align: center;
            padding: 6px;
            border: 1px solid #dee2e6;
        }

        .capacity-high {
            background-color: #fff3cd !important;
            color: #856404 !important;
            font-weight: bold;
        }

        .capacity-over {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            font-weight: bold;
        }

        .capacity-good {
            background-color: #d1edf1 !important;
            color: #0c5460 !important;
            font-weight: bold;
        }

        .measures-section {
            margin-bottom: 8px;
        }

        .section-title {
            background-color: #e9ecef;
            padding: 6px 12px;
            font-weight: bold;
            border-left: 4px solid #ffd5b1;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .unit-note {
            font-size: 0.75em;
            color: #6c757d;
            font-style: italic;
        }

        .compact-table {
            margin-bottom: 5px;
        }

        .compact-table .table {
            margin-bottom: 0;
        }

        .checkbox-cell {
            text-align: center;
            vertical-align: middle;
        }

        .select-all-container {
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .inventory-container {
            max-height: 400px; 
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .inventory-container::-webkit-scrollbar {
            width: 8px;
        }

        .inventory-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .inventory-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .inventory-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .inventory-container {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-control-sm {
            height: calc(1.8125rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .transport-booking-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #d94f00;
        }

        .alert-estado {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            border-left: 5px solid;
        }

        .alert-estado strong {
            font-size: 15px;
        }
    </style>
    <div class="content-wrapper">
        <section class="content mt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white"
                                style="padding: 0.8rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                                <h1 class="card-title mb-0" style="font-size: 1.4rem;">
                                    Remisión de Salida <?php echo $IdRemision; ?>
                                </h1>
                            </div>
                            <div class="card-body" style="padding: 1rem;">
                                <div style="max-width: 100%;">
                                    <div style="width: 100%;">
                                        <div class="row">
                                            <div class="col-12">
                                                <!-- Sección de Transporte y Booking -->
                                                <div class="transport-booking-section">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="tipoTransporte" class="form-label"><strong>Tipo de Transporte:</strong></label>
                                                                <select class="form-control form-control-sm" id="tipoTransporte" name="tipoTransporte" onchange="actualizarMedidasTransporte()">
                                                                    <option value="">Seleccione un transporte</option>
                                                                    <?php foreach ($transportes as $transporte): ?>
                                                                        <option value="<?php echo $transporte->IdTransporte; ?>" 
                                                                            <?php echo ($remisionInfo && $remisionInfo->IdTransporte == $transporte->IdTransporte) ? 'selected' : ''; ?>>
                                                                            <?php echo $transporte->Transporte; ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="booking" class="form-label"><strong>Booking:</strong></label>
                                                                <input type="text" class="form-control form-control-sm" id="booking" name="booking" 
                                                                    value="" 
                                                                    placeholder="Ingrese el número de booking">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mensaje de Estado de Capacidad -->
                                                <div id="mensajeEstadoContainer" class="alert-estado <?php echo $alertClass ?? 'alert-info'; ?>" style="display: <?php echo ($remisionInfo && $remisionInfo->AltoTransporte) ? 'block' : 'none'; ?>;">
                                                    <?php echo $mensajeEstado ?? 'Seleccione un transporte para ver el estado de capacidad.'; ?>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <section>
                                                            <div class="measures-section compact-table">
                                                                <div class="section-title">Información General de la
                                                                    Remisión</div>
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-bordered table-striped info-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>ID Remisión</th>
                                                                                <th>Cliente</th>
                                                                                <th>Almacén</th>
                                                                                <th>Tipo Transporte</th>
                                                                                <th>Booking</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><b><?php echo $remisionInfo->IdRemision ?? 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b><?php echo $remisionInfo->NombreCliente ?? 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b><?php echo $remisionInfo->Almacen ?? 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b id="transporteDisplay"><?php echo $remisionInfo->Transporte ?? 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b id="bookingDisplay">N/A</b>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-bordered table-striped info-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Alto (cm)</th>
                                                                                <th>Ancho (cm)</th>
                                                                                <th>Largo (cm)</th>
                                                                                <th>Volumen Total (cm³)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><b id="altoTransporteDisplay"><?php echo isset($altoTransporteCm) ? number_format($altoTransporteCm, 2) : 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b id="anchoTransporteDisplay"><?php echo isset($anchoTransporteCm) ? number_format($anchoTransporteCm, 2) : 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b id="largoTransporteDisplay"><?php echo isset($largoTransporteCm) ? number_format($largoTransporteCm, 2) : 'N/A'; ?></b>
                                                                                </td>
                                                                                <td><b id="volumenTransporteDisplay"><?php echo isset($volumenTransporteCm3) ? number_format($volumenTransporteCm3, 2) : 'N/A'; ?></b>
                                                                                </td>
                                                                            </tr>
                                                                            <?php if ($remisionInfo && ($remisionInfo->AltoTransporte || $remisionInfo->AnchoTransporte || $remisionInfo->LargoTransporte)): ?>
                                                                            <tr style="background-color: #f8f9fa;">
                                                                                <td colspan="4" class="unit-note"
                                                                                    style="text-align: center; padding: 4px;">
                                                                                    Valores originales en metros:
                                                                                    Alto:
                                                                                    <span id="altoMetrosDisplay"><?php echo number_format($remisionInfo->AltoTransporte, 2); ?></span>m,
                                                                                    Ancho:
                                                                                    <span id="anchoMetrosDisplay"><?php echo number_format($remisionInfo->AnchoTransporte, 2); ?></span>m,
                                                                                    Largo:
                                                                                    <span id="largoMetrosDisplay"><?php echo number_format($remisionInfo->LargoTransporte, 2); ?></span>m
                                                                                </td>
                                                                            </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-bordered table-striped info-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Alto Total (cm)</th>
                                                                                <th>Ancho Total (cm)</th>
                                                                                <th>Largo Total (cm)</th>
                                                                                <th>Volumen Ocupado (cm³)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><b id="altoProductosDisplay"><?php echo number_format($altoProductosCm, 2); ?></b>
                                                                                </td>
                                                                                <td><b id="anchoProductosDisplay"><?php echo number_format($anchoProductosCm, 2); ?></b>
                                                                                </td>
                                                                                <td><b id="largoProductosDisplay"><?php echo number_format($largoProductosCm, 2); ?></b>
                                                                                </td>
                                                                                <td><b id="volumenProductosDisplay"><?php echo number_format($volumenProductosCm3, 2); ?></b>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <?php if ($remisionInfo && ($remisionInfo->AltoTransporte || $remisionInfo->AnchoTransporte || $remisionInfo->LargoTransporte)): ?>
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-bordered table-striped info-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Porcentaje de Uso</th>
                                                                                <th>Estado</th>
                                                                                <th>Capacidad Disponible (cm³)</th>
                                                                                <th>Recomendación</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr id="filaCapacidad" class="<?php echo $capacityClass; ?>">
                                                                                <td><b id="porcentajeUsoDisplay"><?php echo number_format($porcentajeUso, 2); ?>%</b>
                                                                                </td>
                                                                                <td><b id="estadoDisplay"><?php echo $capacityStatus; ?></b>
                                                                                </td>
                                                                                <td><b id="capacidadDisponibleDisplay">
                                                                                        <?php echo isset($capacidadDisponible) ? number_format($capacidadDisponible, 2) : 'N/A'; ?>
                                                                                    </b></td>
                                                                                <td><b id="recomendacionDisplay">
                                                                                        <?php echo $recomendacion ?? 'N/A'; ?>
                                                                                    </b></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resto del código se mantiene igual -->
                        <!-- Sección de Inventario y Seleccionados -->
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="card" style="display: flex; position: relative;" id="cardInventario">
                                    <div class="card-header text-white"
                                        style="padding: 0.8rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                                        <h1 class="card-title mb-0" style="font-size: 1.2rem;">Total de Inventario
                                        </h1>
                                    </div>
                                    <div class="card-body" style="padding: 0.8rem;">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <section>
                                                        <div class="select-all-container">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="selectAllCheckbox">
                                                                <label class="form-check-label"
                                                                    for="selectAllCheckbox">
                                                                    Seleccionar todos los visibles
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="search-container">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    id="searchInput"
                                                                    placeholder="Buscar en inventario...">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary btn-sm"
                                                                        type="button" onclick="searchTable()">
                                                                        <i class="fas fa-search"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-secondary btn-sm"
                                                                        type="button" onclick="clearSearch()">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Contenedor con scroll -->
                                                        <div class="inventory-container">
                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-bordered table-striped table-sm"
                                                                    id="tablaLote" name="tablaLote">
                                                                    <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                                                                        <tr>
                                                                            <th width="auto" class="checkbox-cell"
                                                                                style="color:black; text-align: center;">
                                                                                
                                                                            </th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                CodBarras</th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                MaterialNo</th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                Articulo</th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                Destino</th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                Piezas</th>
                                                                            <th width="auto"
                                                                                style="color:black; text-align: center;">
                                                                                Remision(BK)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="sourceTable">
                                                                        <!-- Los datos se cargarán automáticamente -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        
                                                        <button type="button" class="btn btn-danger btn-sm mt-1"
                                                            style="background-color: #d94f00; !important;"
                                                            id="btnAñadir"
                                                            onclick="añadirRegistros(<?php echo $IdRemisionEncabezado; ?>,'<?php echo $IdRemision; ?>',<?php echo $Almacen; ?>)">Añadir
                                                            Seleccionados</button>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Loading Overlay -->
                                    <div class="loading-overlay" id="loadingInventario" style="display: none;">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="sr-only">Cargando inventario...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--TABLA DE REGISTROS SELECCIONADOS-->
                            <div class="col-6">
                                <div class="card" style="display: flex; position: relative;">
                                    <div class="card-header text-white"
                                        style="padding: 0.8rem; border-bottom: 2px solid #d94f00; background-color: #d94f00 ">
                                        <h1 class="card-title mb-0" style="font-size: 1.2rem;">Seleccionados:</h1>
                                    </div>
                                    <div class="card-body" style="padding: 0.8rem;">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <section>
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-bordered table-striped table-sm"
                                                                id="infolote" name="infolote">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            CodBarras</th>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            MaterialNo</th>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            Articulo</th>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            Destino</th>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            Piezas</th>
                                                                        <th width="auto"
                                                                            style="color:black; text-align: center;">
                                                                            Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tbodySeleccionados">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Loading Overlay -->
                                    <div class="loading-overlay" id="loadingSeleccionados" style="display: none;">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="sr-only">Cargando seleccionados...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Guardar Salida -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <form id="formLote" action="ProcesoSalidaRemision/Salida.php" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="text" name="user" value="<?php echo $IdUsuario; ?>" hidden>
                                    <input type="text" name="IdRemisionEncabezado"
                                        value="<?php echo $IdRemisionEncabezado; ?>" hidden>
                                    <input type="text" name="IdRemision" value="<?php echo $IdRemision; ?>" hidden>
                                    <input type="text" name="Almacen" value="<?php echo $Almacen; ?>" hidden>
                                    
                                    <!-- Campos para transporte y booking que se enviarán en el formulario -->
                                    <input type="hidden" id="IdTransporteInput" name="IdTransporte" value="<?php echo $remisionInfo->IdTransporte ?? ''; ?>">
                                    <input type="hidden" id="BookingInput" name="Booking" value="">

                                    <button type="submit" class="btn btn-danger btn-lg btn-block"
                                        style="background-color: #d94f00; !important;" id="DarSalida">Guardar
                                        Salida</button>
                                </form>
                            </div>
                        </div>

                        <script type="text/javascript">
                        // Datos de transportes para JavaScript
                        const transportesData = <?php echo json_encode($transportesData); ?>;
                        
                        // Variables globales para los datos de productos
                        let altoProductosCm = <?php echo $altoProductosCm ?? 0; ?>;
                        let anchoProductosCm = <?php echo $anchoProductosCm ?? 0; ?>;
                        let largoProductosCm = <?php echo $largoProductosCm ?? 0; ?>;
                        let volumenProductosCm3 = <?php echo $volumenProductosCm3 ?? 0; ?>;

                        function actualizarMedidasTransporte() {
                            const selectTransporte = document.getElementById('tipoTransporte');
                            const selectedId = selectTransporte.value;
                            
                            if (selectedId && transportesData[selectedId]) {
                                const transporte = transportesData[selectedId];
                                
                                // Convertir metros a centímetros
                                const altoCm = transporte.Alto * 100;
                                const anchoCm = transporte.Ancho * 100;
                                const largoCm = transporte.Largo * 100;
                                const volumenCm3 = altoCm * anchoCm * largoCm;
                                
                                // Actualizar displays
                                document.getElementById('transporteDisplay').textContent = transporte.Transporte;
                                document.getElementById('altoTransporteDisplay').textContent = altoCm.toFixed(2);
                                document.getElementById('anchoTransporteDisplay').textContent = anchoCm.toFixed(2);
                                document.getElementById('largoTransporteDisplay').textContent = largoCm.toFixed(2);
                                document.getElementById('volumenTransporteDisplay').textContent = volumenCm3.toFixed(2);
                                
                                // Actualizar valores en metros
                                const altoMetrosDisplay = document.getElementById('altoMetrosDisplay');
                                const anchoMetrosDisplay = document.getElementById('anchoMetrosDisplay');
                                const largoMetrosDisplay = document.getElementById('largoMetrosDisplay');
                                
                                if (altoMetrosDisplay) altoMetrosDisplay.textContent = transporte.Alto.toFixed(2);
                                if (anchoMetrosDisplay) anchoMetrosDisplay.textContent = transporte.Ancho.toFixed(2);
                                if (largoMetrosDisplay) largoMetrosDisplay.textContent = transporte.Largo.toFixed(2);
                                
                                // Calcular y actualizar capacidad
                                actualizarCapacidad(altoCm, anchoCm, largoCm, volumenCm3);
                                
                                // Actualizar campos hidden del formulario
                                document.getElementById('IdTransporteInput').value = selectedId;
                                document.getElementById('BookingInput').value = document.getElementById('booking').value;
                                
                                // Mostrar mensaje de estado
                                document.getElementById('mensajeEstadoContainer').style.display = 'block';
                            } else {
                                // Limpiar displays si no hay transporte seleccionado
                                document.getElementById('transporteDisplay').textContent = 'N/A';
                                document.getElementById('altoTransporteDisplay').textContent = 'N/A';
                                document.getElementById('anchoTransporteDisplay').textContent = 'N/A';
                                document.getElementById('largoTransporteDisplay').textContent = 'N/A';
                                document.getElementById('volumenTransporteDisplay').textContent = 'N/A';
                                document.getElementById('IdTransporteInput').value = '';
                                
                                // Limpiar también la fila de capacidad
                                const filaCapacidad = document.getElementById('filaCapacidad');
                                if (filaCapacidad) {
                                    filaCapacidad.className = '';
                                }
                                document.getElementById('porcentajeUsoDisplay').textContent = 'N/A';
                                document.getElementById('estadoDisplay').textContent = 'SIN TRANSPORTE';
                                document.getElementById('capacidadDisponibleDisplay').textContent = 'N/A';
                                document.getElementById('recomendacionDisplay').textContent = 'SELECCIONE TRANSPORTE';
                                
                                // Ocultar mensaje de estado
                                document.getElementById('mensajeEstadoContainer').style.display = 'none';
                            }
                        }

                        function actualizarCapacidad(altoCm, anchoCm, largoCm, volumenCm3) {
                            const porcentajeUso = volumenCm3 > 0 ? (volumenProductosCm3 / volumenCm3) * 100 : 0;
                            const capacidadDisponible = volumenCm3 - volumenProductosCm3;
                            
                            let capacityClass = '';
                            let capacityStatus = '';
                            let recomendacion = '';
                            let mensajeEstado = '';
                            let alertClass = '';
                            
                            if (porcentajeUso > 100) {
                                capacityClass = 'capacity-over';
                                capacityStatus = 'SOBRECARGADO';
                                recomendacion = 'REDUCIR CARGA';
                                mensajeEstado = '⚠️ <strong>ALERTA:</strong> El transporte está SOBRECARGADO (' + porcentajeUso.toFixed(1) + '%). Por favor retire algunos productos.';
                                alertClass = 'alert-danger';
                            } else if (porcentajeUso > 80) {
                                capacityClass = 'capacity-high';
                                capacityStatus = 'ALTA CAPACIDAD';
                                recomendacion = 'MÁXIMA CAPACIDAD';
                                mensajeEstado = '🔶 <strong>ADVERTENCIA:</strong> El transporte está cerca de su capacidad máxima (' + porcentajeUso.toFixed(1) + '%). Considere no agregar más productos.';
                                alertClass = 'alert-warning';
                            } else if (porcentajeUso > 0) {
                                capacityClass = 'capacity-good';
                                capacityStatus = 'DENTRO DE CAPACIDAD';
                                recomendacion = 'CAPACIDAD ÓPTIMA';
                                mensajeEstado = '✅ <strong>ÓPTIMO:</strong> El transporte tiene capacidad disponible (' + porcentajeUso.toFixed(1) + '% usado). Puede continuar agregando productos.';
                                alertClass = 'alert-success';
                            } else {
                                capacityStatus = 'SIN CARGA';
                                recomendacion = 'SIN PRODUCTOS';
                                mensajeEstado = 'ℹ️ <strong>INFORMACIÓN:</strong> No hay productos cargados en el transporte. Puede comenzar a agregar productos.';
                                alertClass = 'alert-info';
                            }
                            
                            // Actualizar displays de capacidad
                            document.getElementById('porcentajeUsoDisplay').textContent = porcentajeUso.toFixed(2) + '%';
                            document.getElementById('estadoDisplay').textContent = capacityStatus;
                            document.getElementById('capacidadDisponibleDisplay').textContent = capacidadDisponible.toFixed(2);
                            document.getElementById('recomendacionDisplay').textContent = recomendacion;
                            
                            // Actualizar clase de la fila
                            const filaCapacidad = document.getElementById('filaCapacidad');
                            if (filaCapacidad) {
                                filaCapacidad.className = capacityClass;
                            }
                            
                            // Actualizar mensaje de estado
                            const mensajeContainer = document.getElementById('mensajeEstadoContainer');
                            mensajeContainer.innerHTML = mensajeEstado;
                            mensajeContainer.className = 'alert-estado ' + alertClass;
                            mensajeContainer.style.display = 'block';
                            
                            // Mostrar notificación si está cerca del límite o sobrecargado
                            if (porcentajeUso > 100) {
                                mostrarNotificacion('❌ TRANSPORTE SOBRECARGADO', 'El transporte excede su capacidad máxima. Retire productos antes de continuar.', 'error');
                            } else if (porcentajeUso > 95) {
                                mostrarNotificacion('⚠️ CAPACIDAD CRÍTICA', 'El transporte está al ' + porcentajeUso.toFixed(1) + '% de su capacidad. Considere no agregar más productos.', 'warning');
                            } else if (porcentajeUso > 80) {
                                mostrarNotificacion('🔶 ALTA CAPACIDAD', 'El transporte está al ' + porcentajeUso.toFixed(1) + '% de su capacidad. Proceda con precaución.', 'warning');
                            }
                        }

                        function mostrarNotificacion(titulo, mensaje, tipo) {
                            // Usar Toast de SweetAlert2 para notificaciones no intrusivas
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: tipo,
                                title: titulo,
                                text: mensaje
                            });
                        }

                        // Actualizar booking cuando cambie el input
                        document.getElementById('booking').addEventListener('input', function() {
                            document.getElementById('bookingDisplay').textContent = this.value;
                            document.getElementById('BookingInput').value = this.value;
                        });

                        // Función para obtener checkboxes VISIBLES
                        function getVisibleCheckboxes() {
                            const allCheckboxes = document.querySelectorAll('.select-checkbox');
                            const visibleCheckboxes = [];
                            
                            allCheckboxes.forEach(checkbox => {
                                const row = checkbox.closest('tr');
                                if (row && row.style.display !== 'none') {
                                    visibleCheckboxes.push(checkbox);
                                }
                            });
                            
                            return visibleCheckboxes;
                        }

                        // Función para actualizar el estado del checkbox "Seleccionar todos"
                        function updateSelectAllCheckboxState() {
                            const visibleCheckboxes = getVisibleCheckboxes();
                            const visibleCount = visibleCheckboxes.length;
                            
                            if (visibleCount === 0) {
                                document.getElementById('selectAllCheckbox').checked = false;
                                document.getElementById('selectAllCheckbox').disabled = true;
                                return;
                            }
                            
                            document.getElementById('selectAllCheckbox').disabled = false;
                            
                            const checkedCount = visibleCheckboxes.filter(checkbox => checkbox.checked).length;
                            document.getElementById('selectAllCheckbox').checked = checkedCount === visibleCount && visibleCount > 0;
                            document.getElementById('selectAllCheckbox').indeterminate = checkedCount > 0 && checkedCount < visibleCount;
                        }

                        // Función para mostrar/ocultar loading
                        function showLoading(elementId, show) {
                            const loadingElement = document.getElementById(elementId);
                            if (loadingElement) {
                                loadingElement.style.display = show ? 'flex' : 'none';
                            }
                        }

                        // Función para actualizar solo las tablas necesarias
                        function actualizarTablas() {
                            console.log('Actualizando tablas...');
                            cargarInventario();
                            cargarSeleccionados();
                        }

                        // Función para actualizar la información de capacidad
                        function actualizarInformacionCapacidad() {
                            fetch('ProcesoSalidaRemision/obtenerDatosCapacidad.php?IdRemisionEncabezado=<?php echo $IdRemisionEncabezado; ?>')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Actualizar las variables globales con los nuevos datos
                                        altoProductosCm = data.altoTotal;
                                        anchoProductosCm = data.anchoTotal;
                                        largoProductosCm = data.largoTotal;
                                        volumenProductosCm3 = data.volumenTotal;
                                        
                                        console.log('Datos actualizados:', {
                                            alto: altoProductosCm,
                                            ancho: anchoProductosCm,
                                            largo: largoProductosCm,
                                            volumen: volumenProductosCm3
                                        });
                                        
                                        // Actualizar displays de productos inmediatamente
                                        document.getElementById('altoProductosDisplay').textContent = altoProductosCm.toFixed(2);
                                        document.getElementById('anchoProductosDisplay').textContent = anchoProductosCm.toFixed(2);
                                        document.getElementById('largoProductosDisplay').textContent = largoProductosCm.toFixed(2);
                                        document.getElementById('volumenProductosDisplay').textContent = volumenProductosCm3.toFixed(2);
                                        
                                        // Recalcular la capacidad si hay un transporte seleccionado
                                        const transporteSeleccionado = document.getElementById('tipoTransporte').value;
                                        if (transporteSeleccionado && transportesData[transporteSeleccionado]) {
                                            const transporte = transportesData[transporteSeleccionado];
                                            const altoCm = transporte.Alto * 100;
                                            const anchoCm = transporte.Ancho * 100;
                                            const largoCm = transporte.Largo * 100;
                                            const volumenCm3 = altoCm * anchoCm * largoCm;
                                            
                                            actualizarCapacidad(altoCm, anchoCm, largoCm, volumenCm3);
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al actualizar capacidad:', error);
                                });
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            cargarInventario();
                            cargarSeleccionados();

                            document.getElementById('searchInput').addEventListener('keyup', function() {
                                searchTable();
                            });

                            // Event listener CORREGIDO para seleccionar/deseleccionar todos
                            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                                const visibleCheckboxes = getVisibleCheckboxes();
                                const isChecked = this.checked;
                                
                                visibleCheckboxes.forEach(checkbox => {
                                    checkbox.checked = isChecked;
                                });
                            });

                            // Event listener delegado para los checkboxes individuales
                            document.addEventListener('change', function(e) {
                                if (e.target.classList.contains('select-checkbox')) {
                                    updateSelectAllCheckboxState();
                                }
                            });
                        });

                        function cargarInventario() {
                            showLoading('loadingInventario', true);
                            mostrarLote(<?php echo $Almacen; ?>,
                                <?php echo $remisionInfo->Cliente; ?>);
                        }

                        function cargarSeleccionados() {
                            showLoading('loadingSeleccionados', true);
                            mostrarSeleccionados(<?php echo $Almacen; ?>,
                                <?php echo $remisionInfo->Cliente; ?>, <?php echo $IdRemisionEncabezado; ?>,
                                <?php echo $IdRemisionEncabezado; ?>);
                        }

                        function mostrarLote(IdAlmacen, Cliente) {
                            fetch('ObtenerLoteRoyal.php?IdAlmacen=' + IdAlmacen + '&IdCliente=' + Cliente)
                                .then(response => response.json())
                                .then(data => {
                                    const tablaBody = document.querySelector('#tablaLote tbody');
                                    tablaBody.innerHTML = '';

                                    data.forEach(registro => {
                                        const row = document.createElement('tr');

                                        let codBarrasFormateado;
                                        if (registro.EsArmado == 1) {
                                            codBarrasFormateado =
                                                `ARM-${String(registro.CodBarras).padStart(6, '0')}`;
                                        } else {
                                            codBarrasFormateado =
                                                `${String(registro.NumRecinto)}-${String(registro.CodBarras).padStart(6, '0')}`;
                                        }

                                        row.innerHTML = `
                                                <td class="checkbox-cell" style="text-align: center;">
                                                    <input type="checkbox" class="select-checkbox" name="loteSeleccionado[]"
                                                        value="${registro.CodBarras}">
                                                </td>
                                                <td style="text-align: center;" hidden>${registro.CodBarras}</td>
                                                <td style="text-align: center;">${codBarrasFormateado}</td>
                                                <td style="text-align: center;">${registro.MaterialNo}</td>
                                                <td style="text-align: center;">${registro.Articulo}</td>
                                                <td style="text-align: center;">${registro.Destino}</td>
                                                <td style="text-align: center;">${registro.Piezas}</td>
                                                <td style="text-align: center;">${registro.Booking}</td>
                                                <td style="text-align: center;" hidden="${!registro.IdArticulo}">${registro.IdArticulo}</td>
                                                <td style="text-align: center;" hidden="${!registro.Cliente}">${registro.Cliente}</td>
                                            `;
                                        tablaBody.appendChild(row);
                                    });
                                    
                                    // Actualizar el estado del checkbox "Seleccionar todos" después de cargar
                                    updateSelectAllCheckboxState();
                                    showLoading('loadingInventario', false);
                                })
                                .catch(error => {
                                    console.error('Error al cargar el inventario:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo cargar el inventario',
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#d94f00'
                                    });
                                    showLoading('loadingInventario', false);
                                });
                        }

                        function mostrarSeleccionados(IdAlmacen, Cliente, IdRemisionEncabezado) {
                            fetch('obtenerSeleccionadosRoyal.php?IdAlmacen=' + IdAlmacen + '&IdCliente=' + Cliente +
                                    '&IdRemisionEncabezado=' + IdRemisionEncabezado)
                                .then(response => response.json())
                                .then(data => {
                                    const tablaBody = document.querySelector('#tbodySeleccionados');
                                    tablaBody.innerHTML = '';

                                    data.forEach(elegidas => {
                                        let codBarrasFormateado;
                                        if (elegidas.EsArmado == 1) {
                                            codBarrasFormateado =
                                                `ARM-${String(elegidas.CodBarras).padStart(6, '0')}`;
                                        } else {
                                            codBarrasFormateado =
                                                `${String(elegidas.NumRecinto)}-${String(elegidas.CodBarras).padStart(6, '0')}`;
                                        }

                                        const row = document.createElement('tr');
                                        row.innerHTML = `
                                                <td style="text-align: center;">${codBarrasFormateado}</td>
                                                <td width="auto" style="text-align: center;">${elegidas.MaterialNo}</td>
                                                <td width="auto" style="text-align: center;">${elegidas.Articulo}</td>
                                                <td width="auto" style="text-align: center;">${elegidas.Destino}</td>
                                                <td style="text-align: center;">${elegidas.Piezas}</td>
                                                <td width="auto" style="text-align: center;">
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="EliminarRegistro(<?php echo $IdRemisionEncabezado; ?>,${elegidas.CodBarras})">
                                                        Eliminar
                                                    </button>
                                                </td>`;
                                        tablaBody.appendChild(row);
                                    });
                                    showLoading('loadingSeleccionados', false);
                                })
                                .catch(error => {
                                    console.error('Error al cargar los seleccionados:', error);
                                    showLoading('loadingSeleccionados', false);
                                });
                        }

                        function añadirRegistros(IdRemisionEncabezado,IdRemision,Almacen) {
                            const checkboxes = document.querySelectorAll('.select-checkbox:checked');

                            if (checkboxes.length === 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Por favor, selecciona al menos un registro.',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#d94f00'
                                });
                                return;
                            }

                            const registros = [];

                            checkboxes.forEach(checkbox => {
                                const fila = checkbox.parentElement.parentElement;
                                const CodBarras = fila.cells[1].textContent;
                                const MaterialNo = fila.cells[3].textContent;
                                const EsArmado = 0; 
                                const piece = fila.cells[6].textContent;
                                const IdArticulo = fila.cells[8].textContent;
                                const IdCliente = fila.cells[9].textContent;
                                const Estatus = 1;

                                registros.push({
                                    IdRemisionEncabezado: IdRemisionEncabezado,
                                    IdRemision: IdRemision,
                                    Almacen: Almacen,
                                    CodBarras: CodBarras,
                                    MaterialNo: MaterialNo,
                                    piece: piece,
                                    EsArmado: EsArmado,
                                    Estatus: Estatus,
                                    IdArticulo: IdArticulo,
                                    IdCliente: IdCliente
                                });
                            });

                            enviarRegistros(registros);
                        }

                        function enviarRegistros(registros) {
                            showLoading('loadingSeleccionados', true);
                            
                            fetch('ProcesoSalidaRemision/AgregarRegistroBD.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(registros)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: `Se han agregado ${registros.length} registro(s) correctamente.`,
                                            icon: 'success',
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonColor: '#d94f00'
                                        }).then(() => {
                                            // Limpiar checkboxes sin recargar la página
                                            document.querySelectorAll('.select-checkbox').forEach(checkbox => {
                                                checkbox.checked = false;
                                            });
                                            document.getElementById('selectAllCheckbox').checked = false;
                                            
                                            // Actualizar solo las tablas necesarias Y la capacidad inmediatamente
                                            actualizarTablas();
                                            
                                            // Forzar la actualización de capacidad inmediatamente
                                            setTimeout(() => {
                                                actualizarInformacionCapacidad();
                                            }, 500);
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: "Favor de Validar con el Administrador",
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonColor: '#d94f00'
                                        });
                                    }
                                    showLoading('loadingSeleccionados', false);
                                })
                                .catch(error => {
                                    console.error('Error al enviar los datos:', error);
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "Hubo un problema al añadir los registros.",
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#d94f00'
                                    });
                                    showLoading('loadingSeleccionados', false);
                                });
                        }

                       function EliminarRegistro(IdRemisionEncabezado, CodBarras) {
                            Swal.fire({
                                title: '¿Estás seguro?',
                                text: `¿Quieres eliminar el registro con CodBarras: ${CodBarras}?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, eliminar',
                                cancelButtonText: 'Cancelar',
                                confirmButtonColor: '#d94f00',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    showLoading('loadingSeleccionados', true);
                                    
                                    const formData = new FormData();
                                    formData.append('IdRemisionEncabezado', IdRemisionEncabezado);
                                    formData.append('CodBarras', CodBarras);

                                    fetch('ProcesoSalidaRemision/EliminarRegistroBD.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Eliminado',
                                                text: data.message,
                                                icon: 'success',
                                                confirmButtonColor: '#d94f00'
                                            }).then(() => {
                                                // Actualizar solo las tablas necesarias en lugar de recargar toda la página
                                                actualizarTablas();
                                                
                                                // Forzar la actualización de capacidad inmediatamente
                                                setTimeout(() => {
                                                    actualizarInformacionCapacidad();
                                                }, 500);
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: data.message,
                                                confirmButtonColor: '#d94f00'
                                            });
                                        }
                                        showLoading('loadingSeleccionados', false);
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Ocurrió un error al eliminar el registro',
                                            confirmButtonColor: '#d94f00'
                                        });
                                        showLoading('loadingSeleccionados', false);
                                    });
                                }
                            });
                        }

                        function searchTable() {
                            const input = document.getElementById('searchInput');
                            const filter = input.value.toUpperCase();
                            const table = document.getElementById('tablaLote');
                            const tr = table.getElementsByTagName('tr');

                            for (let i = 1; i < tr.length; i++) {
                                let found = false;
                                const td = tr[i].getElementsByTagName('td');
                                for (let j = 0; j < td.length; j++) {
                                    if (td[j]) {
                                        const txtValue = td[j].textContent || td[j].innerText;
                                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            found = true;
                                            break;
                                        }
                                    }
                                }

                                tr[i].style.display = found ? '' : 'none';
                            }
                            
                            // Actualizar el estado del checkbox "Seleccionar todos" después de buscar
                            updateSelectAllCheckboxState();
                        }

                        function clearSearch() {
                            document.getElementById('searchInput').value = '';
                            searchTable();
                        }

                        document.getElementById('formLote').addEventListener('submit', function(e) {
                            e.preventDefault();

                            Swal.fire({
                                title: 'Procesando',
                                html: 'Generando la salida, por favor espere...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(this);

                            fetch('ProcesoSalidaRemision/Salida.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();

                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Éxito',
                                            text: data.message,
                                            confirmButtonColor: '#d94f00',
                                            confirmButtonText: 'Aceptar'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = '../Salidas/SalidasPendientes.php';
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message,
                                            confirmButtonColor: '#d94f00',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Ocurrió un error al procesar la solicitud',
                                        confirmButtonColor: '#d94f00',
                                        confirmButtonText: 'Aceptar'
                                    });
                                    console.error('Error:', error);
                                });
                        });
                        </script>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php include_once '../templates/footer.php' ?>
    <aside class="control-sidebar"></aside>
</div>
</body>
</html>