<?php
   $BaseURL = getenv('BaseURL');
   $VERSION = getenv('VERSION');
   $NUMERO = getenv('NUMERO');
?>
</div>
        <?php include_once $BaseURL . "/templates/scripts.php" ?>
        <?php include_once $BaseURL . "/templates/scripts-datatables.php" ?>
      </body>
   </html>
<footer class="main-footer">
 | Todos los derechos reservados &copy; <?php echo Date('Y');?> <a href="https://www.alpasa.mx/" target="_blank" style="color:darkorange;">ALPASA </a>| 
  <div class="float-right d-none d-sm-inline-block">
     | <b><?php echo strtoupper($VERSION); ?> - VERSIÓN <?php echo $NUMERO; ?> </b> |
  </div>