<?php
// Iniciar sesión para obtener el ID del usuario logueado
session_start();
require_once '../inc/conexion.php'; // Asegúrate de que la ruta a conexion.php es correcta

// Comprobar si el ID del usuario está en la sesión
if (!isset($_SESSION['user_id'])) {
    die("Error: Usuario no logueado.");
}

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consultar todos los posts del usuario logueado utilizando PDO
$query = "SELECT * FROM posts WHERE user_id = :user_id";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Posts Privados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        .header-links {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .header-links a {
            color: #007bff;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }
        .header-links a:hover {
            text-decoration: underline;
        }
        .post-container {
            width: 80%;
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }
        .post {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .post:last-child {
            border-bottom: none;
        }
        .post h3 {
            margin: 0;
            color: #333;
        }
        .post p {
            color: #666;
        }
        .post img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header-links">
        <a href="dashboard.php">Volver al Dashboard</a>
        <a href="admin.php">Crear Nuevo Post</a>
    </div>

    <h1>Posts Privados</h1>

    <?php if (count($posts) > 0): ?>
        <div class="post-container">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($post['descripcion']); ?></p>
                    <?php if (!empty($post['imagen'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($post['imagen']); ?>" alt="Imagen del post">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No has creado ningún post aún.</p>
    <?php endif; ?>
</body>
</html>
