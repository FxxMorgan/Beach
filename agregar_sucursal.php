<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es jefe
if ($_SESSION['rol'] != 'jefe') {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['agregar_sucursal'])) {
    $nombre_sucursal = $_POST['nombre_sucursal'];

    // Insertar la nueva sucursal en la base de datos
    $query = "INSERT INTO sucursales (nombre) VALUES ('$nombre_sucursal')";
    if ($conn->query($query) === TRUE) {
        echo "<script>Swal.fire('Operación Exitosa', 'Sucursal agregada exitosamente', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error', 'Error: " . $conn->error . "', 'error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Sucursal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Agregar Nueva Sucursal</h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <form method="POST">
                <label for="nombre_sucursal" class="block text-gray-700 font-bold mb-2">Nombre de la Sucursal</label>
                <input type="text" id="nombre_sucursal" name="nombre_sucursal" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                <button type="submit" name="agregar_sucursal"
                        class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Agregar Sucursal</button>
            </form>
        </div>
    </div>
</body>
</html>