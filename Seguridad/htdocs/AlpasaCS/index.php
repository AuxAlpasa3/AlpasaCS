<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>AlpasaCS</title>    
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <div id="imagen" style="background-image: url(Fondos/fondo.jpg);  ">

        <div id="contenedor">
            <div id="central">
                <div id="login">
                    <div class="titulo">
                        <img id="Logo" src="Fondos/Logo3.png" alt="Logo Alpasa" width="400"  />
                    </div>
                       <form name="form1" action="Control/verificar-usuario.php" method="POST" class="col-12" id="form1"> 
                    <div class="form-group">
                        <input type="text" name="usuario" placeholder="Usuario" pattern="^[A-Za-z]+$" required>
                        
                        <input type="password" placeholder="Contraseña" name="password" required>
                        
                        <button type="submit" title="Ingresar" name="Ingresar" id="btnInicio"><i class="fas fa-sign-in-alt"></i> Iniciar Sesion</button>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>
