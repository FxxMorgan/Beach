<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['sucursal_id']) || !isset($_SESSION['rol'])) {
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 200px;
            width: 200px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Dashboard - Sucursal: <?php echo isset($sucursal['nombre']) ? $sucursal['nombre'] : 'No seleccionada'; ?></h1>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <nav class="flex flex-wrap justify-center space-x-4 mb-6">
                <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Ventas</a>
                <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Inventarios</a>
                <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Gastos</a>
                <?php if ($rol == 'TI'): ?>
                    <a href="administrar_usuarios.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Administrar Usuarios</a>
                    <a href="administrar_sucursales.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Administrar Sucursales</a>
                <?php endif; ?>
            </nav>
            <?php if ($rol == 'TI' || $rol == 'jefe' || $rol == 'encargado'): ?>
                <h2>Bienvenido, <?php echo ucfirst($rol); ?></h2>
                <p>Acceso a la sucursal: <?php echo isset($sucursal['nombre']) ? $sucursal['nombre'] : 'No seleccionada'; ?></p>

                <!-- Formulario de selección de sucursal (solo para jefes y TI) -->
                <?php if ($rol == 'jefe' || $rol == 'TI'): ?>
                    <form method="POST" class="mb-6">
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

                <!-- Charts Section -->
                <div class="charts grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="chart-container">
                        <canvas id="ventasChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="inventariosChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="gastosChart"></canvas>
                    </div>
                </div>

                <!-- Chart.js Script -->
                <script>
                    var ctxVentas = document.getElementById('ventasChart').getContext('2d');
                    var ventasChart = new Chart(ctxVentas, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($ventas_labels); ?>,
                            datasets: [{
                                label: 'Ventas',
                                data: <?php echo json_encode($ventas_data); ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    var ctxInventarios = document.getElementById('inventariosChart').getContext('2d');
                    var inventariosChart = new Chart(ctxInventarios, {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($inventarios_labels); ?>,
                            datasets: [{
                                label: 'Inventarios',
                                data: <?php echo json_encode($inventarios_data); ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    var ctxGastos = document.getElementById('gastosChart').getContext('2d');
                    var gastosChart = new Chart(ctxGastos, {
                        type: 'pie',
                        data: {
                            labels: <?php echo json_encode($gastos_labels); ?>,
                            datasets: [{
                                label: 'Gastos',
                                data: <?php echo json_encode($gastos_data); ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>

            <?php else: ?>
                <p>No tienes permisos para acceder a esta sección.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>