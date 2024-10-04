<?php
session_start();

// Si el usuario ya está autenticado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Si el usuario no está autenticado, mostrar el formulario de login
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta para verificar al usuario
    $query = "SELECT * FROM usuarios WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['contraseña'])) {
            // Iniciar sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['sucursal_id'] = $user['sucursal_id'];  // Sucursal vinculada al usuario

            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <h1>Inicio de sesión</h1>
    <!-- Formulario de login -->
    <form method="POST">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="Correo electrónico" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required><br>

        <button type="submit" name="login">Iniciar sesión</button>
    </form>
</body>
</html>
