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
$sucursal_id = $_SESSION['sucursal_id'];  // Sucursal por defecto
$rol = $_SESSION['rol'];

// Si es jefe o TI, puede seleccionar cualquier sucursal
if (($rol == 'jefe' || $rol == 'TI') && isset($_POST['sucursal_id'])) {
    $sucursal_id = $_POST['sucursal_id'];  // Sucursal seleccionada
}

// Obtener el nombre de la sucursal seleccionada
$query = "SELECT nombre FROM sucursales WHERE id='$sucursal_id'";
$sucursal = $conn->query($query)->fetch_assoc();

// Obtener todas las sucursales (solo para jefes y TI)
if ($rol == 'jefe' || $rol == 'TI') {
    $sucursales_query = "SELECT id, nombre FROM sucursales";
    $sucursales = $conn->query($sucursales_query);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Dashboard - Sucursal: <?php echo $sucursal['nombre']; ?></h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <?php if ($rol == 'TI' || $rol == 'jefe' || $rol == 'encargado'): ?>
                <h2>Bienvenido, <?php echo ucfirst($rol); ?></h2>
                <p>Acceso a la sucursal: <?php echo $sucursal['nombre']; ?></p>

                <!-- Formulario de selección de sucursal (solo para jefes y TI) -->
                <?php if ($rol == 'jefe' || $rol == 'TI'): ?>
                    <form method="POST">
                        <select name="sucursal_id" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                            <?php while ($row = $sucursales->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $sucursal_id) ? 'selected' : ''; ?>>
                                    <?php echo $row['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver sucursal</button>
                    </form>
                    <hr>
                <?php endif; ?>

                <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>" class="text-indigo-600 hover:text-indigo-800">Ver Ventas</a> |
                <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>" class="text-indigo-600 hover:text-indigo-800">Ver Inventarios</a> |
                <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>" class="text-indigo-600 hover:text-indigo-800">Ver Gastos</a>
                <?php if ($rol == 'TI'): ?>
                    | <a href="administrar_usuarios.php" class="text-indigo-600 hover:text-indigo-800">Administrar Usuarios</a>
                    | <a href="administrar_sucursales.php" class="text-indigo-600 hover:text-indigo-800">Administrar Sucursales</a>
                <?php endif; ?>

            <?php else: ?>
                <p>No tienes permisos para acceder a esta sección.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>