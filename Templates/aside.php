<?php
$VERSION = getenv('VERSION');
$BaseURLIMG = getenv('BaseURLIMG');

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
  <!-- Brand Logo -->
  <div class="text-center">
    <a href="#" class="brand">
      <img src="<?php echo $BaseURLIMG; ?>/dist/img/LogoAlpasaBlanco.png" width="70%" alt="ALPASA" class="brand-image">
      <span class="brand-text font-weight-bold" style="color: orangered;"></span>
    </a>
  </div>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo $BaseURLIMG; ?>/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="<?php echo $BaseURLIMG; ?>/Menu/Index" class="d-block" style="color: #dc5504;">
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
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>ACCESOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Empleados/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Empleados</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Vehiculos/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Vehiculos</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Visitas/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Visitas</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Maniobras/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Maniobras</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Proveedores/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Proveedores</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Accesos/Externos/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Externos</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>CATALOGOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Catalogos/Empleados/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Empleados</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Catalogos/Vehiculos/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Vehiculos</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/atalogos/Visitas/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Visitas</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Catalogos/Maniobras/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Maniobras</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Catalogos/Proveedores/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Proveedores</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo $BaseURLIMG; ?>/Catalogos/Externos/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Externos</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>

        <!-- MÓDULO CONFIGURACIÓN -->
        <?php if ($isConfigUser) { ?>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fa-solid fa-table-list" style="color: #dc5504;"></i>
              <p style="color: #dc5504;">CONFIGURACIÓN
                <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <!-- USUARIOS -->
              <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>USUARIOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Usuarios" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Usuarios</p>
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/TipoUsuario" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Tipo de Usuarios</p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              <?php } ?>

              <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>CATALOGOS INTERNOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Almacen" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Empresa</p>
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Almacen" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Almacen</p>
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Almacen" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Departamento</p>
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Almacen" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Cargos</p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              <?php } ?>

              <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>BITACORA</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="<?php echo $BaseURLIMG; ?>/Config/Bitacora" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Bitacora</p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              <?php } ?>
            </li>
          <?php } ?>
        </ul>
      </nav>
    </div>
  </aside>