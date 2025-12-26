<!-- footer.php -->
        </div> <!-- Cierre de content-wrapper -->
    </div> <!-- Cierre de wrapper -->
    
    <!-- Footer fijo -->
    <footer class="main-footer fixed-bottom bg-light border-top py-2">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    | Todos los derechos reservados &copy; <?php echo Date('Y'); ?> 
                    <a href="https://www.alpasa.mx/" target="_blank" class="text-warning">ALPASA</a> |
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <?php 
                        $VERSION = getenv('VERSION');
                        $NUMERO = getenv('NUMERO');
                    ?>
                    | <b><?php echo strtoupper($VERSION); ?> - VERSIÓN <?php echo $NUMERO; ?></b> |
                </div>
            </div>
        </div>
    </footer>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    
    <!-- DataTables Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    

    
    <!-- Scripts adicionales -->
    <?php if(file_exists('scripts.php')): ?>
        <?php include_once 'scripts.php'; ?>
    <?php endif; ?>
    
    <?php if(file_exists('scripts-datatables.php')): ?>
        <?php include_once 'scripts-datatables.php'; ?>
    <?php endif; ?>
</body>
</html>