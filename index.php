<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("VIEWS/carro.gif") no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
        }

        header {
            width: 100%;
            position: fixed;
            top: 0;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            display: flex;
            justify-content: flex-end;
            padding: 20px;
            position: absolute;
            width: 100%;
            top: 0;
            background-color: white;
        }
        a {
            color: black;
            text-decoration: none;
            font-size: 20px;
            margin-left: 20px;
        }
        h1 {
            font-size: 64px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .boton-principal {
            background-color: #28a745;
            padding: 15px 30px;
            font-size: 20px;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .boton-principal:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <header>
        <a href="VIEWS/login.php">Login</a>
        <a href="VIEWS/registro.php">Registrar</a>
    </header>

    <div class="contenido">
        <h1>Bienvenido a Mi Pagina Web</h1>
        <p>Explora todo lo que tenemos para ofrecer</p>
        <button class="boton-principal">Descubre Más</button>
    </div>

    <footer>
        &copy; 2024 Tu Empresa. Todos los derechos reservados.
    </footer>

</body>
</html>
