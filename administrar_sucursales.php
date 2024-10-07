<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es jefe o TI
if ($_SESSION['rol'] != 'jefe' && $_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

// Obtener las sucursales
$sucursales_query = "SELECT * FROM sucursales";
$sucursales_result = $conn->query($sucursales_query);

if (isset($_POST['agregar_sucursal'])) {
    $nombre = $_POST['nombre'];
    
    // Insertar la nueva sucursal en la base de datos
    $query = "INSERT INTO sucursales (nombre) VALUES ('$nombre')";
    if ($conn->query($query) === TRUE) {
        echo "Sucursal agregada exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['editar_sucursal'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    
    // Actualizar la sucursal en la base de datos
    $query = "UPDATE sucursales SET nombre='$nombre' WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        echo "Sucursal actualizada exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['eliminar_sucursal'])) {
    $id = $_POST['id'];
    
    // Eliminar la sucursal de la base de datos
    $query = "DELETE FROM sucursales WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        echo "Sucursal eliminada exitosamente";
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
    <title>Administrar Sucursales</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Administrar Sucursales</h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <form method="POST">
                <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre de la Sucursal</label>
                <input type="text" id="nombre" name="nombre" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                <button type="submit" name="agregar_sucursal"
                        class="w-full bg-green-600 text-white p-3 rounded-lg font-bold hover:bg-green-700">Agregar Sucursal</button>
            </form>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
            <h2 class="text-2xl font-bold mb-5">Sucursales</h2>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Nombre</th>
                        <th class="py-3 px-4 border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sucursales_result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4 border-b"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-4 border-b">
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" class="w-full p-2 border rounded">
                        </td>
                        <td class="py-3 px-4 border-b">
                                <button type="submit" name="editar_sucursal" class="bg-blue-600 text-white p-2 rounded">Actualizar</button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="eliminar_sucursal" class="bg-red-600 text-white p-2 rounded">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="dashboard.php" class="block mt-4 text-center text-blue-500 hover:underline">Volver al Dashboard</a>
    </div>
</body>
</html>