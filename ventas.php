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

// Ajustar la zona horaria a la hora local (por ejemplo, 'America/Santiago' para Chile)
date_default_timezone_set('America/Santiago');

$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id'];
$success_message = "";

// Obtener el nombre de la sucursal
$sucursal_query = "SELECT nombre FROM sucursales WHERE id='$sucursal_id'";
$sucursal_result = $conn->query($sucursal_query);
$sucursal = $sucursal_result->fetch_assoc();

// Función de auditoría
function auditoria($conn, $accion, $usuario_id) {
    $fecha = date('Y-m-d H:i:s');

    // Obtener el nombre del usuario
    $usuario_query = "SELECT nombre FROM usuarios WHERE id='$usuario_id'";
    $usuario_result = $conn->query($usuario_query);

    // Depuración
    if ($usuario_result) {
        echo "Usuario query ejecutado correctamente.<br>";
    } else {
        echo "Error en usuario query: " . $conn->error . "<br>";
    }

    $usuario = $usuario_result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];

    // Depuración
    echo "Usuario ID: $usuario_id, Nombre: $usuario_nombre<br>";

    $query = "INSERT INTO auditoria (usuario_id, usuario_nombre, accion, fecha) VALUES ('$usuario_id', '$usuario_nombre', '$accion', '$fecha')";
    if ($conn->query($query) === TRUE) {
        echo "Auditoría registrada correctamente.<br>";
    } else {
        echo "Error en auditoría: " . $conn->error . "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto = str_replace('.', '', $_POST['monto']); // Eliminar puntos para convertir a entero
    $comentario = $_POST['comentario'];
    $fecha = date('Y-m-d H:i:s');
    $usuario_id = $_SESSION['usuario_id'];

    $insert_query = "INSERT INTO ventas (fecha, monto, usuario_id, sucursal_id, comentario) VALUES ('$fecha', '$monto', '$usuario_id', '$sucursal_id', '$comentario')";
    if ($conn->query($insert_query) === TRUE) {
        auditoria($conn, "Venta registrada: Monto: $monto, Comentario: $comentario, Fecha: $fecha, Sucursal ID: $sucursal_id", $usuario_id);
        $success_message = "Venta registrada exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Obtener ventas y nombres de usuarios
$query = "SELECT v.*, u.nombre as usuario_nombre FROM ventas v JOIN usuarios u ON v.usuario_id = u.id WHERE v.sucursal_id='$sucursal_id'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($success_message): ?>
            alert("<?php echo $success_message; ?>");
            <?php endif; ?>
        });
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Ventas - Sucursal: <?php echo $sucursal['nombre']; ?></h1>
        <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
            <form method="POST" class="mb-6">
                <div class="mb-4">
                    <label for="monto" class="block text-gray-700 font-bold mb-2">Monto</label>
                    <input type="text" id="monto" name="monto" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="275.100">
                </div>
                <div class="mb-4">
                    <label for="comentario" class="block text-gray-700 font-bold mb-2">Comentario</label>
                    <select id="comentario" name="comentario" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Cierra día">Cierra día</option>
                        <option value="Retiro cajas">Retiro cajas</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Registrar Venta</button>
            </form>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Fecha</th>
                        <th class="py-3 px-4 border-b">Monto</th>
                        <th class="py-3 px-4 border-b">Usuario</th>
                        <th class="py-3 px-4 border-b">Sucursal</th>
                        <th class="py-3 px-4 border-b">Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4 border-b"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['fecha']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo "$" . number_format($row['monto'], 0, '', '.'); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['usuario_nombre']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $sucursal['nombre']; ?></td>
                        <td class="py-3 px-4 border-b"><?php echo $row['comentario']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="mt-6">
                <a href="dashboard.php" class="w-full bg-gray-600 text-white p-3 rounded-lg font-bold hover:bg-gray-700 inline-block text-center">Volver al Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>