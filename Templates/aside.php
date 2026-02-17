<?php
$VERSION = getenv('VERSION');

function sanitizeOutput($data)
{
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

$currentUser = $_SESSION['current_user' . $VERSION] ?? '';
$currentRole = $_SESSION['rol_current_users' . $VERSION] ?? 0;

$isAdmin = in_array($currentRole, [1, 2, 3]);
$isConfigUser = in_array($currentRole, [1, 2, 3, 7]);
$isNotLimited = in_array($currentRole, [1, 2, 3, 7, 4]);
$isNotClient = in_array($currentRole, [6]);

?>

<aside class="main-sidebar sidebar-light-primary elevation-4"> 
  <div class="text-center">
    <a href="#" class="brand">
      <img src="../dist/img/LogoAlpasaC.png" width="70%" alt="ALPASA" class="brand-image">
      <span class="brand-text font-weight-bold" style="color: orangered;"></span>
    </a>
  </div>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="../Menu/Index" class="d-block" style="color: #dc5504;">
          <?php echo sanitizeOutput($currentUser); ?>
        </a>
      </div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- MÓDULO PROCESOS -->
        <?php if ($isNotLimited) { ?>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fa-solid fa-table-list" style="color: #dc5504;"></i>
              <p style="color: #dc5504;">PROCESOS
                <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <!-- CATALOGOS -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>CATALOGOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="../Empleados/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Empleados</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../Externos/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Personal Externo</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../Proveedores/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Proveedores</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../Visitas/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Visitas</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../Maniobras/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Maniobras</p>
                        </a>
                      </li>
                      <!-- <li class="nav-item">
                        <a href="../Bascula/Catalogos" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Bascula</p>
                        </a>
                      </li> -->
                  </ul>
                </li>
              <?php } ?>
              <!-- ACCESOS -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>ACCESOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="../Empleados/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Empleados</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Externos/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Personal Externo</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Proveedores/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Proveedores</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Visitas/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Visitas</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Maniobras/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Maniobras</p>
                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="../Bascula/Accesos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Bascula</p>
                      </a>
                    </li> -->
                  </ul>
                </li>
              <?php } ?>

              
            </ul>
          </li>
        <?php } ?>

        <?php if ($isConfigUser) { ?>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fa-solid fa-table-list" style="color: #dc5504;"></i>
              <p style="color: #dc5504;">CONFIGURACIÓN
                <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <!-- <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>USUARIOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Config/Usuarios" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Usuarios</p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              <?php } ?> -->

              <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>UBICACION</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Config/UbicacionInterna" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Ubicaciones Internas</p>
                        </a>
                      </li>
                    <?php } ?>
                    <li class="nav-item">
                      <a href="../Config/Ubicacion" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Ubicaciones</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>

      </ul>
    </nav>
  </div>
</aside>