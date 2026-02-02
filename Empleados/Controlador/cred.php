<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gafete Digital ALPASA</title>
    <style>
        /* Eliminamos margenes y aseguramos que el cuerpo ocupe el 100% */
        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden; /* Evita scroll innecesario */
            font-family: 'Arial Black', Gadget, sans-serif;
        }

        .card-full {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            background-color: white;
        }

        /* Sección Superior - Naranja */
        .header {
            background-color: #d9530f;
            flex: 0 1 15%; /* Ocupa el 15% de la altura */
            display: flex;
            align-items: flex-end; /* Alinea el logo hacia abajo para que toque el borde blanco */
            justify-content: center;
        }

        .logo-box {
            background-color: white;
            padding: 10px 30px;
            border-radius: 4px 4px 0 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 900;
            color: #222;
        }

        /* Sección Central - QR */
        .content {
            flex: 1; /* Ocupa el resto del espacio disponible */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .qr-code {
            width: 80vw; /* El QR ocupará el 80% del ancho de la pantalla */
            max-width: 400px;
            aspect-ratio: 1 / 1;
            background-image: url('https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=ID_COLABORADOR_123');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Sección Inferior - Naranja */
        .footer {
            background-color: #d9530f;
            flex: 0 1 20%; /* Ocupa el 20% de la altura */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 0 10px;
        }

        .name {
            font-size: 1.5rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .id {
            font-size: 1.2rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="card-full">
        <header class="header">
            <div class="logo-box">
                <svg width="40" height="40" viewBox="0 0 100 100" style="fill:#d9530f">
                    <path d="M10 90 L10 40 L50 10 L90 40 L90 90 Z M30 80 L70 80 L70 50 L30 50 Z" />
                </svg>
                <span class="logo-text">ALPASA</span>
            </div>
        </header>

        <main class="content">
            <div class="qr-code"></div>
        </main>

        <footer class="footer">
            <div class="name">NOMBRE COLABORADOR</div>
            <div class="id">IDPERSONAL</div>
        </footer>
    </div>

</body>
</html>

</body>
</html>