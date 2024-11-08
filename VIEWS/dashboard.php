<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ruta absoluta
$ruta_absoluta = $_SESSION['user_imagen'];

// Determinar si la imagen es válida
if (empty($ruta_absoluta) || !file_exists($ruta_absoluta)) {
    // Ruta de imagen por defecto
    $ruta_relativa = 'ruta/a/imagen/por/defecto.png'; // Asegúrate de que esta ruta sea correcta
    $perfil_sin_foto = true; // Indicador para mostrar "Perfil sin foto"
} else {
    // Convertir a ruta relativa
    $ruta_relativa = str_replace('C:\xampp\htdocs\curso_php\mi-proyecto\VIEWS/', '', $ruta_absoluta);
    $perfil_sin_foto = false; // Indicador de que hay foto
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Estilos generales */
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            height: 100vh;
        }

        /* Header */
        header {
            width: 100%;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            background-color: #e9e9e9;
            border-bottom: 1px solid #ccc;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 0;
        }

        header a {
            color: black;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            font-size: 16px;
        }

        /* Texto de bienvenida */
        h2 {
            font-size: 32px;
            margin: 20px 0 10px;
        }

        /* Texto del rol */
        p {
            font-size: 24px;
            margin: 10px 0;
        }

        /* Imagen de perfil */
        .profile-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-image: url('<?php echo htmlspecialchars($ruta_relativa); ?>');
            background-size: cover;
            background-position: center;
            margin-top: 20px;
            border: 3px solid black;
        }

        /* Estilo para el texto "Perfil sin foto" */
        .perfil-sin-foto {
            font-size: 18px;
            color: red; /* Puedes cambiar el color según tu preferencia */
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Encabezado con enlaces de Administración y Cerrar Sesión -->
    <header>
        <a href="admin.php">Administración</a> |
        <a href="../logout.php">Cerrar Sesión</a>
    </header>

    <!-- Elementos centrados en el cuerpo -->
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    <p>Rol: <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
    
    <!-- Div de la imagen de perfil -->
    <div class="profile-image" style="background-image: url('<?php echo $perfil_sin_foto ? "SIN IMAGEN PERFIL.jpg" : htmlspecialchars($ruta_relativa); ?>');"></div>
    
    <?php if ($perfil_sin_foto): ?>
        <p class="perfil-sin-foto">Perfil sin foto</p>
    <?php endif; ?>
</body>
</html>