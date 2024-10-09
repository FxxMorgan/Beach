<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es TI
if ($_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

// Obtener los registros de auditoría
$auditoria_query = "SELECT * FROM auditoria";
$auditoria_result = $conn->query($auditoria_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Registros de Auditoría</h1>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Usuario</th>
                        <th class="py-3 px-4 border-b">Acción</th>
                        <th class="py-3 px-4 border-b">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $auditoria_result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4 border-b"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['usuario']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['accion']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['fecha']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="dashboard.php" class="block mt-4 text-center text-blue-500 hover:underline">Volver al Dashboard</a>
    </div>
</body>
</html>