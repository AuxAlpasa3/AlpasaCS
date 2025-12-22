<?php
include_once "../../templates/Sesion.php";

$IdRemision = isset($_GET['IdRemision']) ? $_GET['IdRemision'] : '';
$IdRemisionEncabezado = isset($_GET['IdRemisionEncabezado']) ? $_GET['IdRemisionEncabezado'] : '';
$IdAlmacen = isset($_GET['IdAlmacen']) ? $_GET['IdAlmacen'] : '';

try {
    $conn = $Conexion;

    $sql = "SELECT 
                h.IdRemisionLineaHistorial,
                h.IdLinea,
                h.Piezas,
                h.Comentario,
                h.FechaCambio,
                h.Usuario,
                h.TipoCambio,
                u.NombreColaborador as NombreUsuario,
                a.Material as Articulo,
                a.MaterialNo,
                h.Booking
            FROM t_remision_linea_historial h
            LEFT JOIN t_usuario u ON h.Usuario = u.IdUsuario
            LEFT JOIN t_articulo a ON h.IdArticulo = a.IdArticulo
            WHERE h.IdRemision = ?
            ORDER BY h.FechaCambio DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$IdRemision]);
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $movimientos = [];
    $error = "Error al cargar los movimientos históricos: " . $e->getMessage();
}
?>

<div class="modal fade" id="movimientosHistoricos" tabindex="-1" role="dialog"
    aria-labelledby="movimientosHistoricosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="border-bottom: 2px solid; background-color: #6c757d">
                <h5 class="modal-title" style="font-size: 1.2rem; margin: 0;">
                    <i class="fa fa-history"></i> MOVIMIENTOS HISTÓRICOS - REMISIÓN:
                    <b><?php echo htmlspecialchars($IdRemision); ?></b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($movimientos)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle"></i> No hay movimientos históricos registrados para esta remisión.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%" style="color:black; text-align: center;">#</th>
                                    <th width="15%" style="color:black; text-align: center;">Fecha</th>
                                    <th width="8%" style="color:black; text-align: center;">IdLinea</th>
                                    <th width="12%" style="color:black; text-align: center;">Material No.</th>
                                    <th width="20%" style="color:black; text-align: center;">Artículo</th>
                                    <th width="8%" style="color:black; text-align: center;">Piezas</th>
                                    <th width="8%" style="color:black; text-align: center;">Booking</th>
                                    <th width="10%" style="color:black; text-align: center;">Tipo Movimiento</th>
                                    <th width="12%" style="color:black; text-align: center;">Usuario</th>
                                    <th width="20%" style="color:black; text-align: center;">Comentario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $index => $movimiento): ?>
                                    <tr>
                                        <td class="text-center" style="color:black;"><?php echo $index + 1; ?></td>
                                        <td class="text-center">
                                            <?php
                                            $fecha = new DateTime($movimiento['FechaCambio']);
                                            echo $fecha->format('d/m/Y H:i:s');
                                            ?>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($movimiento['IdLinea']); ?></td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($movimiento['MaterialNo'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($movimiento['Articulo'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($movimiento['Piezas']); ?></td>
                                         <td class="text-center">
                                            <?php echo htmlspecialchars($movimiento['Booking'] ?? 'N/A'); ?>
                                        </td><td class="text-center">
                                            <?php
                                            $tipo = $movimiento['TipoCambio'];
                                            $badge_class = 'secondary';
                                            if ($tipo == 'INSERT')
                                                $badge_class = 'success';
                                            if ($tipo == 'UPDATE')
                                                $badge_class = 'warning';
                                            if ($tipo == 'DELETE')
                                                $badge_class = 'danger';
                                            ?>
                                            <span class="badge badge-<?php echo $badge_class; ?>">
                                                <?php echo htmlspecialchars($tipo); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($movimiento['NombreUsuario'] ?? 'Usuario ' . $movimiento['Usuario']); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($movimiento['Comentario'])): ?>
                                                <?php echo htmlspecialchars($movimiento['Comentario']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>