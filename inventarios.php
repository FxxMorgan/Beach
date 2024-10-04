<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['sucursal_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id'];
$rol = $_SESSION['rol'];

// Verificar acceso a la sucursal
if ($rol != 'TI' && $sucursal_id != $_SESSION['sucursal_id']) {
    echo "No tienes permisos para acceder a esta sucursal.";
    exit();
}

$query = "SELECT * FROM inventarios WHERE sucursal_id='$sucursal_id'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Inventarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Inventarios - Sucursal: <?php echo $sucursal_id; ?></h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2">ID</th>
                        <th class="py-2">Descripción</th>
                        <th class="py-2">Cantidad</th>
                        <th class="py-2">Fecha</th>
                        <th class="py-2">Usuario ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2"><?php echo $row['id']; ?></td>
                        <td class="py-2"><?php echo $row['descripcion']; ?></td>
                        <td class="py-2"><?php echo $row['cantidad']; ?></td>
                        <td class="py-2"><?php echo $row['fecha']; ?></td>
                        <td class="py-2"><?php echo $row['usuario_id']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>