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
  <!-- Brand Logo -->
  <div class="text-center">
    <a href="#" class="brand">
      <img src="../dist/img/LogoAlpasaBlanco.png" width="70%" alt="ALPASA" class="brand-image">
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
              <!-- REMISIÓN -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>REMISION</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="../Remision/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Remisiones</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Remision/RemisionProceso" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Estatus Remision</p>
                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="../Remision/AsignarRemisiones" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Asignar Remision</p>
                      </a>
                    </li> -->
                    <li class="nav-item">
                      <a href="../Remision/CancelarRemision" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Cancelar Remisión en Proceso</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>

              <!-- INGRESOS -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>INGRESOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Ingresos/Index" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Ingresos</p>
                        </a>
                      </li>
                    <?php } ?>
                    <li class="nav-item">
                      <a href="../Ingresos/IngresosPendientes" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Ingresos Pendientes</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Ingresos/IngresosFinalizados" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Ingresos Finalizados</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Ingresos/TarjaIngreso" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Tarja Ingreso</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Ingresos/FotografiasIngresos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Fotografias Ingresos</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Ingresos/ImprimirQrMultiple" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Imprimir QR por CodBarras</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Ingresos/ImprimirQR" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">ImprimirQR´s por Tarjas</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>

              <!-- SALIDAS -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>SALIDAS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Salidas/Index" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Salidas</p>
                        </a>
                      </li>
                    <?php } ?>
                    <li class="nav-item">
                      <a href="../Salidas/SalidasPendientes" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Salidas Pendientes</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Salidas/SalidasFinalizadas" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Salidas Finalizadas</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Salidas/TarjaSalida" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Tarja Salida</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Salidas/FotografiasSalidas" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Fotografias Salidas</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>

              <!-- INVENTARIO -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>INVENTARIO</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="../Inventario/IndexRoyal" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">GENERAL</p>
                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="../Revision/Index" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">REVISION INVENTARIO</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Inventario/EnviarCorreos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">ENVIAR CORREO</p>
                      </a>
                    </li> -->
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
                        <a href="../Config/Usuarios" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Usuarios</p>
                        </a>
                      </li>
                    <?php } ?>
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Config/TipoUsuario" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Tipo de Usuarios</p>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              <?php } ?>

              <!-- ALMACEN -->
              <?php if ($isConfigUser) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>ALMACEN</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <?php if ($isAdmin) { ?>
                      <li class="nav-item">
                        <a href="../Config/Almacen" class="nav-link">
                          <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                          <p style="color: #dc5504;">Almacenes</p>
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

        <!-- MÓDULO CATÁLOGOS -->
        <?php if ($isNotLimited) { ?>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fa-solid fa-table-list" style="color: #dc5504;"></i>
              <p style="color: #dc5504;">CATALOGOS
                <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <!-- ARTICULOS -->
              <?php if ($isNotLimited) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>ARTICULOS</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="../Catalogos/Articulos" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Articulos</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Catalogos/TipoMaterial" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Tipo de Material</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Catalogos/TipoEmbalaje" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Tipo de Embalaje</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Catalogos/ReglaEstiba" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Reglas de Estiba</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Catalogos/Periodicidad" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Periodicidad</p>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>

              <!-- CLIENTES -->
              <?php if ($isNotClient) { ?>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <b>CLIENTES</b>
                    <i class="fas fa-angle-left right" style="color: #dc5504;"></i>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="../Catalogos/Clientes" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Clientes</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="../Catalogos/GrupoClientes" class="nav-link">
                        <i class="fa-solid fa-file-text" style="color: #dc5504;"></i>
                        <p style="color: #dc5504;">Grupo de Clientes</p>
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