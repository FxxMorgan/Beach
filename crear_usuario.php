<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 'jefe' && $_SESSION['rol'] != 'TI')) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'beach');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Ajustar zona horaria
date_default_timezone_set('America/Santiago');

// Función de auditoría
function auditoria($conn, $accion, $usuario_id) {
    $fecha = date('Y-m-d H:i:s');
    $usuario_nombre = $_SESSION['usuario'];

    $query = "INSERT INTO auditoria (usuario_id, usuario_nombre, accion, fecha) VALUES ('$usuario_id', '$usuario_nombre', '$accion', '$fecha')";
    $conn->query($query);
}

// Obtener las sucursales para mostrarlas en el formulario
$sucursales = $conn->query("SELECT id, nombre FROM sucursales");

if (isset($_POST['agregar_usuario'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash de la contraseña
    $rol = $_POST['rol'];
    $sucursal_id = $_POST['sucursal_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Insertar el nuevo usuario en la base de datos
    $query = "INSERT INTO usuarios (nombre, email, contraseña, rol, sucursal_id) 
              VALUES ('$nombre', '$email', '$password', '$rol', '$sucursal_id')";
    if ($conn->query($query) === TRUE) {
        auditoria($conn, "Usuario agregado: $nombre ($email), Rol: $rol, Sucursal ID: $sucursal_id", $usuario_id);
        echo "Usuario agregado exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Agregar Nuevo Usuario</h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <form method="POST">
                <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                <input type="text" id="nombre" name="nombre" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="email" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" id="email" name="email" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="password" class="block text-gray-700 font-bold mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="rol" class="block text-gray-700 font-bold mb-2">Rol</label>
                <select id="rol" name="rol" required
                        class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <option value="jefe">Jefe</option>
                    <option value="encargado">Encargado</option>
                </select>

                <label for="sucursal_id" class="block text-gray-700 font-bold mb-2">Sucursal</label>
                <select id="sucursal_id" name="sucursal_id" required
                        class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <?php while ($row = $sucursales->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="agregar_usuario"
                        class="w-full bg-green-600 text-white p-3 rounded-lg font-bold hover:bg-green-700">Agregar Usuario</button>
            </form>
            <a href="dashboard.php" class="block mt-4 text-center text-blue-500 hover:underline">Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>