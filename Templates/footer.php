</div>
        <?php include_once 'scripts.php' ?>
        <?php include_once 'scripts-datatables.php' ?>
        <?php $VERSION= getenv('VERSION'); ?>
        <?php $NUMERO= getenv('NUMERO'); ?>
      </body>
   </html>
<footer class="main-footer">
 | Todos los derechos reservados &copy; <?php echo Date('Y');?> <a href="https://www.alpasa.mx/" target="_blank" style="color:darkorange;">ALPASA </a>| 
  <div class="float-right d-none d-sm-inline-block">
     | <b><?php echo strtoupper($VERSION); ?> - VERSIÓN <?php echo $NUMERO; ?> </b> |
  </div>