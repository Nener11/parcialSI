<?php
// Iniciar sesión para obtener el ID del usuario logueado
session_start();
require_once '../inc/conexion.php';

// Consultar todos los posts del usuario logueado
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM posts WHERE user_id = :user_id";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminar post si se envía la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && isset($_POST['ajax'])) {
    $delete_id = $_POST['delete_id'];
    $deleteQuery = "DELETE FROM posts WHERE id = :delete_id AND user_id = :user_id";
    $deleteStmt = $conexion->prepare($deleteQuery);
    $deleteStmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
    $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $deleteStmt->execute();

    echo json_encode(['success' => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Posts Creados</title>
    <div class="header-links">
        <a href="dashboard.php">Volver al Dashboard</a>
        <a href="admin.php">Crear Nuevo Post</a>
    </div>
</head>
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
        background-color: white;
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
    h1 {
        margin-bottom: 20px;
    }
    .post-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr); /* 5 columnas por fila */
        gap: 20px;
        width: 100%;
    }

    .post {
        background-color: #fff;
        border: 2px solid #ccc;
        border-radius: 8px;
        overflow: hidden;
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 300px;
    }
    .post-header {
        background-color: red;
        color: black;
        font-weight: bold;
        padding: 10px;
        font-size: 16px;
        margin: 12px 15px 10px 15px;
    }
    .post img {
        width: 87%;
        height: 200px;
        object-fit: cover;
        border-bottom: 2px solid #ccc;
        margin: 0 10px 0 15px;
        border-radius: 15px;
    }
    .post-description {
        padding: 5px 10px;
        color: #333;
        font-size: 14px;
        background-color: red;
        display: inline-block;
        margin: 10px 14px;
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        word-wrap: break-word;
    }
    .post-description.expanded {
        max-height: none;
        overflow: visible;
        white-space: normal;
    }
    .post-buttons {
        display: flex;
        justify-content: left;
        padding: 10px;
    }
    .post-buttons button {
        padding: 5px 10px;
        border: none;
        color: white;
        cursor: pointer;
        font-weight: none;
        border-radius: 4px;
    }
    .post-buttons .delete-btn {
        background-color:   #f60e0c;
        margin-right: 10px;
    }
    .post-buttons .edit-btn {
        background-color:  #11d563 ;
    }
</style>
<body>

<h1>Posts Creados</h1>

<div class="post-container">
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post" id="post-<?php echo $post['id']; ?>">
                <div class="post-header"><?php echo htmlspecialchars($post['titulo']); ?></div>
                <?php if (!empty($post['imagen'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($post['imagen']); ?>" alt="Imagen del post">
                <?php endif; ?>
                <div class="post-description" onclick="toggleDescription(this)">
                    <?php echo htmlspecialchars($post['descripcion']); ?>
                </div>
                <div class="post-buttons">
                    <!-- Botón de Eliminar con AJAX -->
                    <button onclick="eliminarPost(<?php echo $post['id']; ?>)" class="delete-btn">Eliminar</button>
                    <!-- Botón de Modificar -->
                    <form method="GET" action="modificarpost.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="edit-btn">Modificar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No has creado ningún post aún.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function eliminarPost(postId) {
        if (confirm('¿Estás seguro de que deseas eliminar este post?')) {
            $.ajax({
                url: '', // El URL actual donde se encuentra el archivo PHP
                type: 'POST',
                data: {
                    delete_id: postId,
                    ajax: true // Indicador para diferenciar la solicitud AJAX
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Post eliminado exitosamente');
                        // Eliminar el post del DOM sin recargar
                        $(`#post-${postId}`).remove();
                    } else {
                        alert('Ocurrió un error al eliminar el post');
                    }
                },
                error: function() {
                    alert('Error en la solicitud AJAX');
                }
            });
        }
    }

    function toggleDescription(element) {
        element.classList.toggle("expanded");
    }
</script>

</body>
</html>
