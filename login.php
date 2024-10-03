<?php
session_start();
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

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Mostrar el hash de la contraseña para depuración (solo temporal)
        echo "Hash de la contraseña almacenada: " . $user['contraseña'] . "<br>";

        // Comparar la contraseña ingresada con el hash
        if (password_verify($password, $user['contraseña'])) {
            // La contraseña es correcta, iniciar sesión
            echo "Contraseña correcta<br>";
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['sucursal_id'] = $user['sucursal_id'];  // Sucursal vinculada al usuario

            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            // Contraseña incorrecta
            echo "Contraseña incorrecta<br>";
        }
    } else {
        // Usuario no encontrado
        echo "Usuario no encontrado<br>";
    }
}
?>

<!-- Formulario de login -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Iniciar sesión</h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <form method="POST">
                <label for="email" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo electrónico" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="password" class="block text-gray-700 font-bold mb-2">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <button type="submit" name="login" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
