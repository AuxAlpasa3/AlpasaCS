<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Administrador de Seguridad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <section class="h-100">
             <div id="contenedor">
                <div id="central">
                    <div class="text-center">
                        <h3>Administrador de Seguridad</h3>
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4">Iniciar Sesión</h1>
                            <form method="POST" action="Control/verificar-usuario.php" >
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="usuario">Usuario</label>
                                    <input id="usuario" type="text" class="form-control" name="usuario"  required autofocus>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted" for="password">Contraseña</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" required>
                                </div>

                                <div class="d-flex align-items-center">
                                    <button type="submit" class="btn btn-primary ms-auto">
                                        Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-5 text-muted">
                        Copyright &copy; 2024  &mdash; Almacenamiento y Logística Portuaria de Altamira S.A. de C.V. 
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="Control/index.js"></script>
</body>
</html>