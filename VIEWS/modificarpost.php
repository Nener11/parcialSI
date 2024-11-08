<?php
session_start();
require_once '../inc/funciones.php';
require_once '../inc/conexion.php';

if (!verificar_rol('admin')) {
    echo "Acceso denegado.";
    exit;
}

$errorTitulo = $errorDescripcion = $errorImagen = "";
$titulo = $descripcion = $imagenActual = "";

// Obtén los datos del post actual
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($post) {
        $titulo = $post['titulo'];
        $descripcion = $post['descripcion'];
        $imagenActual = $post['imagen'];
    }
}

// Manejo del formulario para modificar un post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    if (empty($titulo)) {
        $errorTitulo = "Ingrese un título.";
    }
    if (empty($descripcion)) {
        $errorDescripcion = "Ingrese una descripción.";
    }

    // Subida de imagen opcional
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = basename($_FILES["imagen"]["name"]);
        $directorioDestino = "../uploads/";
        $rutaArchivo = $directorioDestino . $nombreImagen;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaArchivo)) {
            $imagenActual = $nombreImagen;
        } else {
            $errorImagen = "Error en la subida de la imagen.";
        }
    }

    if (empty($errorTitulo) && empty($errorDescripcion)) {
        try {
            $consulta = "UPDATE posts SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen WHERE id = :id";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':imagen', $imagenActual);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: posts_creados.php");
                exit;
            } else {
                echo "Error en la actualización de datos.";
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
    <title>Modificar Post</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        header {
            position: absolute;
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
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            resize: vertical;
            font-size: 16px;
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
        img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <a href="posts_creados.php">Regresar</a>
    </header>

    <div class="container">
        <h2>Modificar Post</h2>
        
        <form method="POST" action="modificarpost.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($titulo); ?>">
            <span class="error"><?php echo $errorTitulo; ?></span>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="6"><?php echo htmlspecialchars($descripcion); ?></textarea>
            <span class="error"><?php echo $errorDescripcion; ?></span>

            <label>Imagen actual:</label>
            <?php if ($imagenActual): ?>
                <img src="../uploads/<?php echo htmlspecialchars($imagenActual); ?>" alt="Imagen actual">
            <?php else: ?>
                <p>No hay imagen actual.</p>
            <?php endif; ?>

            <label for="imagen">Imagen (opcional):</label>
            <input type="file" name="imagen" id="imagen">
            <span class="error"><?php echo $errorImagen; ?></span>

            <button type="submit" class="button">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
