<?php
session_start();
if ($_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener todas las sucursales
$query = "SELECT * FROM sucursales";
$result = $conn->query($query);
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
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2">ID</th>
                        <th class="py-2">Nombre</th>
                        <th class="py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2"><?php echo $row['id']; ?></td>
                        <td class="py-2"><?php echo $row['nombre']; ?></td>
                        <td class="py-2">
                            <a href="editar_sucursal.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-800">Editar</a> |
                            <a href="eliminar_sucursal.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-800">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="agregar_sucursal.php" class="text-indigo-600 hover:text-indigo-800">Agregar Sucursal</a>
        </div>
    </div>
</body>
</html>