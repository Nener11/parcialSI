<?php
session_start();
require_once '../inc/funciones.php';
require_once '../inc/conexion.php'; // Asegúrate de incluir la conexión a la base de datos

if (!verificar_rol('admin')) {
    echo "Acceso denegado.";
    exit;
}

$errorTitulo = $errorDescripcion = $errorImagen = $mensajeExito = "";

// Manejo del formulario para crear un post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $user_id = $_SESSION['user_id'];

    if (empty($titulo)) {
        $errorTitulo = "Ingrese un título.";
    }
    if (empty($descripcion)) {
        $errorDescripcion = "Ingrese una descripción.";
    }

    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = basename($_FILES["imagen"]["name"]);
        $directorioDestino = "../uploads/"; // Ajusta la ruta de la carpeta de destino
        $rutaArchivo = $directorioDestino . $nombreImagen;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaArchivo)) {
            // Archivo subido exitosamente
        } else {
            $errorImagen = "Error en la subida de la imagen.";
        }
    } else {
        $errorImagen = "Ingrese una imagen.";
    }

    if (empty($errorTitulo) && empty($errorDescripcion) && empty($errorImagen)) {
        try {
            $consulta = "INSERT INTO posts (titulo, descripcion, imagen, user_id) VALUES (:titulo, :descripcion, :imagen, :user_id)";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':imagen', $nombreImagen);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $mensajeExito = "¡Post creado con éxito!";
                // Redirigir a la página de posts creados
                header("Location: posts_creados.php");
                exit; // Asegúrate de llamar a exit después de header para detener la ejecución del script
            } else {
                echo "Error en la inserción de datos.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Ajusta para centrar el contenido verticalmente */
            background-color: #f0f0f0; /* Color de fondo opcional */
        }
        header {
            position: absolute; /* Posiciona el encabezado en la parte superior */
            top: 10px; 
            right: 10px;
        }
        a {
            padding-right: 20px;
            text-decoration: none;
            color: black;
            font-size: 20px;
        }
        .container {
            width: 400px;
            padding: 20px;
            background-color: #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .error {
            color: red;
            font-size: 0.8em;
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
            text-align: left;
        }
        .button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        
    </style>
</head>
<body>
    <header>
        <a href="dashboard.php">Volver al Dashboard</a>
        <a href="posts_creados.php">Posts Creados</a>
    </header>

    <div class="container">
        <h2>Área de Administración</h2>
        <p>Formulario para la creación de un post.</p>
        
        <form method="POST" action="admin.php" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo">
            <span class="error"><?php echo $errorTitulo; ?></span>

            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion">
            <span class="error"><?php echo $errorDescripcion; ?></span>

            <label for="imagen">Imagen:</label>
            <input type="file" name="imagen" id="imagen">
            <span class="error"><?php echo $errorImagen; ?></span>

            <button type="submit" class="button">Crear</button>
            <span class="error"><?php echo $mensajeExito; ?></span>
        </form>
    </div>
</body>
</html>
