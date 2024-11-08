<?php
session_start();
require_once '../inc/conexion.php';
require_once '../inc/funciones.php';

$errores = [
    'nombre' => '',
    'email' => '',
    'password' => '',
    'imagen_perfil' => '',
    'exito' => ''
];

$nombre = '';
$email = '';
$password = '';
$rol = 'viewer'; // Rol predeterminado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_dato($_POST['nombre']);
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];
    $rol = limpiar_dato($_POST['rol']); // Obtener el rol del formulario

    // Validaciones
    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email no es válido.';
    }
    if (strlen($password) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if (!in_array($rol, ['admin', 'viewer'])) {
        $errores['rol'] = 'Rol no válido.';
    }

    // Validar si se subió una imagen (ya no es obligatorio)
    $targetFilePath = null; // Inicializar la variable para la imagen

    if (!empty($_FILES['imagen_perfil']['name'])) {
        // Validar el tipo y tamaño de la imagen
        $fileType = $_FILES['imagen_perfil']['type'];
        $fileSize = $_FILES['imagen_perfil']['size'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($fileType, $allowedTypes)) {
            $errores['imagen_perfil'] = 'El formato de la imagen no es válido. Debe ser JPG, PNG o GIF.';
        }

        if ($fileSize > 2 * 1024 * 1024) { // Tamaño máximo de 2MB
            $errores['imagen_perfil'] = 'La imagen no debe superar los 2MB.';
        }
    }

    // Verificar si el email ya existe en la base de datos
    $sqlVerificacion = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $stmtVerificacion = $conexion->prepare($sqlVerificacion);
    $stmtVerificacion->bindParam(':email', $email);
    $stmtVerificacion->execute();
    $emailExiste = $stmtVerificacion->fetchColumn();

    if ($emailExiste) {
        $errores['email'] = 'El correo electrónico ya está registrado.';
    }

    // Si no hay errores, proceder con el registro
    if (empty(array_filter($errores))) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Manejar la subida de la imagen
        $uploadDir = '../uploads/'; // Directorio donde se almacenará la imagen
        
        // Verificar si el directorio existe y crear si no
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
        }

        if (!empty($_FILES['imagen_perfil']['name'])) {
            $fileName = basename($_FILES['imagen_perfil']['name']);
            $targetFilePath = $uploadDir . $fileName;

            // Mover el archivo a la ubicación deseada
            if (move_uploaded_file($_FILES['imagen_perfil']['tmp_name'], $targetFilePath)) {
                // La imagen se ha subido correctamente
            } else {
                $errores['imagen_perfil'] = 'Hubo un problema al subir la imagen.';
            }
        }

        // Guardar en la base de datos, permitiendo NULL para la imagen
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, imagen) VALUES (:nombre, :email, :password, :rol, :imagen)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':rol', $rol);
        // Si no hay imagen, insertar NULL
        $stmt->bindParam(':imagen', $targetFilePath); // Guardar la ruta de la imagen o NULL

        if ($stmt->execute()) {
            $errores['exito'] = 'Usuario registrado exitosamente.';
        } else {
            echo "Error al registrar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .caja {
            display: grid;
            place-items: center;
            min-height: 100vh;
        }
        header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 50px;
        }
        a {
            padding-right: 20px;
            text-decoration: none;
            color: black;
            font-size: 27px;
        }
        form {
            width: 300px;
            padding: 20px;
            background-color: white;
        }
        h2 {
            text-align: center;
        }
        .exito {
            text-align: center;
            color: green;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        /* Estilos para el contenedor del label del rol */
        .rol-label-container {
            padding: 10px;
            display: inline-block;
            margin-bottom: 15px;
            width: 140px; /* Ajusta este ancho si lo deseas */
            border: 1px solid #c2c2c2;
            box-sizing: border-box; /* Asegura que padding y border estén incluidos en el ancho/alto */
            height: 40px; /* Ajusta este alto si lo deseas */
            margin-right: 10px;
        }
        /* Estilos para el label del rol */
        .rol-label {
            font-size: 15px;
            text-align: center;
        }
        /* Estilos para el contenedor del select del rol */
        .rol-select-container {
            position: absolute;
            display: inline-block;
            width: 150px; /* Ajusta este ancho si lo deseas */
            border: 1px solid #c2c2c2;
            box-sizing: border-box; /* Asegura que padding y border estén incluidos en el ancho/alto */
            height: 40px; /* Ajusta este alto si lo deseas */
            padding: 6 0 0 15;
        }
        /* Estilos para el select del rol */
        .rol-select {
            border: 1px solid black;
            border-radius: 5px;
            width: 80%; /* Mantiene el ancho completo del contenedor */
            height: 25px; /* Ajusta la altura para que sea más pequeña */
            box-sizing: border-box; /* Incluye padding y border en el ancho y alto total */
            appearance: auto; /* Mantiene el estilo por defecto del navegador para el select */
            padding: 5px; /* Espaciado interno */
            font-size: 12px; /* Tamaño de fuente más pequeño */
            margin-left: 15px;
            margin-top: 7px;
        }
        /* Estilos para el contenedor del label de la imagen */
        .imagen-label-container {
            padding: 10px;
            display: inline-block;
            margin-bottom: 15px;
            width: 140px; /* Ajusta este ancho si lo deseas */
            border: 1px solid #c2c2c2;
            box-sizing: border-box; /* Asegura que padding y border estén incluidos en el ancho/alto */
            height: 40px; /* Ajusta este alto si lo deseas */
            margin-right: 10px;
        }
        /* Estilos para el label de la imagen */
        .imagen-label {
            font-size: 16px;
            text-align: center;
            line-height: 30px; /* Para centrar verticalmente el texto */
            font-size: 15px;
        }
        /* Estilos para el contenedor del input de la imagen */
        .imagen-select-container {
            position: absolute;
            display: inline-block;
            width: 150px; /* Ajusta este ancho si lo deseas */
            border: 1px solid #c2c2c2;
            box-sizing: border-box; /* Asegura que padding y border estén incluidos en el ancho/alto */
            height: 40px; /* Ajusta este alto si lo deseas */
            padding: 6 0 0 15;
        }
        /* Estilos para el input de la imagen */
        .imagen-select {
            border: 1px solid black;
            border-radius: 5px;
            width: 80%; /* Mantiene el ancho completo del contenedor */
            height: 25px; /* Ajusta la altura para que sea más pequeña */
            box-sizing: border-box; /* Incluye padding y border en el ancho y alto total */
            appearance: auto; /* Mantiene el estilo por defecto del navegador para el select */
            padding: 5px; /* Espaciado interno */
            font-size: 12px; /* Tamaño de fuente más pequeño */
            margin-left: 15px;
            margin-top: 7px;
        }
    </style>
</head>
<body>
    <header>
        <a href="../index.php">Index</a>
        <a href="login.php">Login</a>
    </header>

    <div class="caja">
        <form method="post" enctype="multipart/form-data"> <!-- Agregar enctype para subir archivos -->
        <h2>Registro de Usuario</h2>
        <?php if (!empty($errores['exito'])): ?>
            <p class="exito"><?php echo $errores['exito']; ?></p>
        <?php endif; ?>
        
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" >
        <?php if (!empty($errores['nombre'])): ?>
            <p class="error"><?php echo $errores['nombre']; ?></p>
        <?php endif; ?>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" >
        <?php if (!empty($errores['email'])): ?>
            <p class="error"><?php echo $errores['email']; ?></p>
        <?php endif; ?>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" >
        <?php if (!empty($errores['password'])): ?>
            <p class="error"><?php echo $errores['password']; ?></p>
        <?php endif; ?>

        <!-- Campo de selección de Rol -->
        <div class="rol-label-container">
            <label for="rol" class="rol-label">Rol:</label>
        </div>
        <div class="rol-select-container">
            <select name="rol" id="rol" class="rol-select">
                <option value="viewer" <?php echo ($rol === 'viewer') ? 'selected' : ''; ?>>Invitado</option>
                <option value="admin" <?php echo ($rol === 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>

        <!-- Contenedor para la imagen de perfil -->
        <div class="imagen-label-container">
            <label for="imagen_perfil" class="rol-label">Imagen de Perfil:</label>
        </div>
        <div class="imagen-select-container">
            <input type="file" name="imagen_perfil" id="imagen_perfil" class="imagen-select">
            <?php if (!empty($errores['imagen_perfil'])): ?>
                <p class="error"><?php echo $errores['imagen_perfil']; ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Registrar</button>
    </form>

    </div>
</body>
</html>