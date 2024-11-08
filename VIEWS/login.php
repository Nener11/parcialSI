<?php
session_start();
require_once '../inc/conexion.php';
require_once '../inc/funciones.php';

$errores = [
    'error' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];

    // Consultamos si el email existe
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nombre'];
        $_SESSION['user_role'] = $usuario['rol'];
        $_SESSION['user_email'] = $usuario['email'];
        // Reto imagen
        $_SESSION['user_imagen'] = $usuario['imagen'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        // echo "Email o contraseña incorrectos.";
        $errores['error'] = 'Email o contraseña incorrectos.';

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            background-image: url("fondo1.gif"); /* Añade la ruta de tu imagen de fondo */
            background-position: center;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .caja {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }

        form {
            background: rgba(255, 255, 255, 0.7); /* Fondo blanco semi-transparente */
            padding: 20px;
            border: 1px solid blue; /* Borde rojo */
            border-top-right-radius: 50px;
            border-bottom-left-radius: 5px;
            width: 300px;
            position: relative;
            top: 40px; /* Ajusta la posición para dejar espacio para el GIF */
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF; /* Botón azul */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Imagen GIF circular encima del formulario */
        .imagen-circular {
            width: 150px;
            height: 150px;
            background-image: url("mapache.gif");
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            position: absolute;
            top: 160px; /* Coloca el GIF encima del formulario */
            left: calc(49% - 49px); /* Centra la imagen circular horizontalmente */
            border: 3px solid black; /* Añade un borde blanco alrededor del GIF */

        }

        header {
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
    </style>
</head>
<body>
    <header>
        <a href="index.php">Index</a>
        <a href="/VIEWS/registro.php">Registrar</a>
    </header>

    <div class="caja">
        <div class="imagen-circular"></div> <!-- Imagen circular con GIF -->
        <form method="post">
            <h2>Inicio de Sesión</h2>

            <?php if (!empty($errores['error'])): ?>
                <p class="error"><?php echo $errores['error']; ?></p>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>


