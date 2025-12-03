<?php
$VERSION = getenv('VERSION') ?: '';

if (!isset($_SESSION['idusuario' . $VERSION]) || !isset($_SESSION['rol_current_users' . $VERSION])) {
    header('Location: ../login.php');
    exit();
}

$idUsuario = htmlspecialchars($_SESSION['idusuario' . $VERSION], ENT_QUOTES, 'UTF-8');
$rolUsuario = htmlspecialchars($_SESSION['rol_current_users' . $VERSION], ENT_QUOTES, 'UTF-8');
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars" style="color: #dc5504;"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <input type="hidden" id="role" value="<?php echo $rolUsuario; ?>">
        <input type="hidden" id="idusuario" value="<?php echo $idUsuario; ?>">

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt" style="color: #dc5504;"></i>
            </a>
        </li>

        <li class="nav-item">
            <a href="../MovilVersiones/descarga_apk.php" class="nav-link">
                <i class="fas fa-mobile-alt mr-1" style="color: #dc5504;"></i> App Móvil
            </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
               role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="far fa-user" style="color: #dc5504;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <span class="dropdown-header">
                    <small>Bienvenido</small><br>
                    <strong>ALPASA</strong>
                </span>
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="../api/login/logout.php">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutLinks = document.querySelectorAll('a[href*="logout.php"]');
    
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea cerrar sesión?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<style>
.navbar-nav .dropdown-menu {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #dc5504;
}

.dropdown-header {
    font-size: 0.875rem;
    color: #6c757d;
}

.nav-link:hover {
    background-color: rgba(220, 85, 4, 0.1);
}
</style>