<?php
session_start();

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['sucursal_id']) || !isset($_SESSION['rol'])) {
    header('Location: login.php');
    exit();
}

// Función para obtener la conexión a la base de datos
function get_db_connection() {
    $conn = new mysqli('localhost', 'root', '', 'beach');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    return $conn;
}

$conn = get_db_connection();

$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_SESSION['sucursal_id'];
$rol = $_SESSION['rol'];

// Validar y sanitizar el sucursal_id enviado por POST
if (($rol == 'jefe' || $rol == 'TI') && isset($_POST['sucursal_id']) && is_numeric($_POST['sucursal_id'])) {
    $sucursal_id = (int) $_POST['sucursal_id'];
}

// Obtener el nombre de la sucursal seleccionada
$query = $conn->prepare("SELECT nombre FROM sucursales WHERE id = ?");
$query->bind_param('i', $sucursal_id);
$query->execute();
$sucursal = $query->get_result()->fetch_assoc();

// Obtener lista de sucursales para los roles jefe y TI
$sucursales = null;
if ($rol == 'jefe' || $rol == 'TI') {
    $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
    $sucursales_query->execute();
    $sucursales = $sucursales_query->get_result();
}

// Validación del rango de tiempo
$time_range = isset($_POST['time_range']) ? $_POST['time_range'] : 'day';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
$date_condition = '';
$date_format = '%d/%m/%Y'; // Formato de fecha diario

// Configuración de formato y condición de fecha según la selección del usuario
switch ($time_range) {
    case 'day':
        $date_format = '%d/%m/%Y';
        break;
    case 'year':
        $date_format = '%Y';
        break;
    case 'custom':
        if ($start_date && $end_date) {
            $date_format = '%d/%m/%Y';
            $date_condition = "AND fecha BETWEEN ? AND ?";
        }
        break;
    case 'month':
    default:
        $date_format = '%m/%Y';
        break;
}

// Función para obtener datos de la base de datos
function get_data($conn, $table, $sucursal_id, $date_format, $date_condition, $column, $start_date = null, $end_date = null) {
    if ($date_condition) {
        $query = $conn->prepare("SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? $date_condition GROUP BY periodo");
        $query->bind_param('iss', $sucursal_id, $start_date, $end_date);
    } else {
        $query = $conn->prepare("SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? GROUP BY periodo");
        $query->bind_param('i', $sucursal_id);
    }
    $query->execute();
    return $query->get_result();
}

// Obtener datos de ventas, inventarios y gastos
$ventas_result = get_data($conn, 'ventas', $sucursal_id, $date_format, $date_condition, 'monto', $start_date, $end_date);
$inventarios_result = get_data($conn, 'inventarios', $sucursal_id, $date_format, $date_condition, 'cantidad', $start_date, $end_date); // 'cantidad' en vez de 'monto'
$gastos_result = get_data($conn, 'gastos', $sucursal_id, $date_format, $date_condition, 'monto', $start_date, $end_date);

// Procesar datos para Chart.js
function process_data($result) {
    $data = [];
    $labels = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['periodo'];
        $data[] = $row['total'];
    }
    return [$labels, $data];
}

list($ventas_labels, $ventas_data) = process_data($ventas_result);
list($inventarios_labels, $inventarios_data) = process_data($inventarios_result);
list($gastos_labels, $gastos_data) = process_data($gastos_result);

// Cerrar conexión
$conn->close();
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
            height: 400px; /* Increased height */
            width: 100%; /* Increased width to full */
            padding-bottom: 120px; /* Added padding for labels */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Dashboard - Sucursal: <?php echo isset($sucursal['nombre']) ? $sucursal['nombre'] : 'No seleccionada'; ?></h1>
        <div class="max-w-8xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <nav class="flex flex-wrap justify-center space-x-4 mb-6">
                <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Ventas</a>
                <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Inventarios</a>
                <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Ver Gastos</a>
                <?php if ($rol == 'TI'): ?>
                    <a href="administrar_usuarios.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Administrar Usuarios</a>
                    <a href="administrar_sucursales.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Administrar Sucursales</a>
                <?php endif; ?>
            </nav>

            <!-- Formulario de selección de rango de tiempo -->
            <form method="POST" class="mb-6 bg-white p-6 rounded-lg shadow-md">
                <label for="time_range" class="block text-gray-700 font-bold mb-2">Seleccione el rango de tiempo:</label>
                <select name="time_range" id="time_range" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <option value="day">Día</option>
                    <option value="month">Mes</option>
                    <option value="year">Año</option>
                    <option value="custom">Periodo personalizado</option>
                </select>
                
                <div id="custom_dates" class="hidden mb-4">
                    <label for="start_date" class="block text-gray-700 font-bold mb-2">Fecha de inicio:</label>
                    <input type="date" name="start_date" id="start_date" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-2">
                    
                    <label for="end_date" class="block text-gray-700 font-bold mb-2">Fecha de fin:</label>
                    <input type="date" name="end_date" id="end_date" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-300">Aplicar</button>
                
                <p id="loadingMessage" class="text-gray-500 mt-4 hidden">Actualizando gráficos, por favor espere...</p>
            </form>

            <script>
                document.getElementById('time_range').addEventListener('change', function () {
                    var customDates = document.getElementById('custom_dates');
                    if (this.value === 'custom') {
                        customDates.classList.remove('hidden');
                    } else {
                        customDates.classList.add('hidden');
                    }
                });

                // Mostrar mensaje de carga al enviar el formulario
                document.querySelector('form').addEventListener('submit', function () {
                    document.getElementById('loadingMessage').classList.remove('hidden');
                });
            </script>

            <!-- Sección de gráficos -->
            <div class="charts grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="chart-container bg-white p-4 rounded-lg shadow-md">
                    <p class="font-bold text-lg mb-2">Ventas</p>
                    <select name="ventasChartType" id="ventasChartType" class="mb-6 w-full p-2 border rounded">
                        <option value="bar">Barra</option>
                        <option value="line">Linea</option>
                        <option value="pie">Pie</option>
                    </select>
                    <canvas id="ventasChart"></canvas>
                </div>
                <div class="chart-container bg-white p-4 rounded-lg shadow-md">
                    <p class="font-bold text-lg mb-2">Inventarios</p>
                    <select name="inventariosChartType" id="inventariosChartType" class="mb-6 w-full p-2 border rounded">
                        <option value="bar">Barra</option>
                        <option value="line">Linea</option>
                        <option value="pie">Pie</option>
                    </select>
                    <canvas id="inventariosChart"></canvas>
                </div>
                <div class="chart-container bg-white p-4 rounded-lg shadow-md">
                    <p class="font-bold text-lg mb-2">Gastos</p>
                    <select name="gastosChartType" id="gastosChartType" class="mb- w-full p-2 border rounded">
                        <option value="bar">Barra</option>
                        <option value="line">Linea</option>
                        <option value="pie">Pie</option>
                    </select>
                    <canvas id="gastosChart"></canvas>
                </div>
            </div>

            <!-- Script de Chart.js -->
            <script>
                function createChart(ctx, type, labels, data) {
                    return new Chart(ctx, {
                        type: type,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Datos',
                                data: data,
                                backgroundColor: type === 'pie' ? 
                                    [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ] : 'rgba(75, 192, 192, 0.2)',
                                borderColor: type === 'pie' ? 
                                    [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ] : 'rgba(75, 192, 192, 1)',
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
                }

                // Inicialización de gráficos
                var ctxVentas = document.getElementById('ventasChart').getContext('2d');
                var ventasChart = createChart(ctxVentas, 'bar', <?php echo json_encode($ventas_labels); ?>, <?php echo json_encode($ventas_data); ?>);

                var ctxInventarios = document.getElementById('inventariosChart').getContext('2d');
                var inventariosChart = createChart(ctxInventarios, 'line', <?php echo json_encode($inventarios_labels); ?>, <?php echo json_encode($inventarios_data); ?>);

                var ctxGastos = document.getElementById('gastosChart').getContext('2d');
                var gastosChart = createChart(ctxGastos, 'pie', <?php echo json_encode($gastos_labels); ?>, <?php echo json_encode($gastos_data); ?>);

                // Almacenar datos y etiquetas globalmente
                window.ventasChartLabels = <?php echo json_encode($ventas_labels); ?>;
                window.ventasChartData = <?php echo json_encode($ventas_data); ?>;
                window.inventariosChartLabels = <?php echo json_encode($inventarios_labels); ?>;
                window.inventariosChartData = <?php echo json_encode($inventarios_data); ?>;
                window.gastosChartLabels = <?php echo json_encode($gastos_labels); ?>;
                window.gastosChartData = <?php echo json_encode($gastos_data); ?>;

                // Actualizar tipo de gráfico al seleccionar
                document.querySelectorAll('select[id$="ChartType"]').forEach(select => {
                    select.addEventListener('change', function() {
                        var chartType = this.value;
                        var chartId = this.name.replace('ChartType', 'Chart');
                        var ctx = document.getElementById(chartId).getContext('2d');

                        // Destruir el gráfico anterior
                        if (window[chartId]) {
                            window[chartId].destroy();
                        }

                        // Crear nuevo gráfico con el tipo seleccionado
                        window[chartId] = createChart(ctx, chartType, window[chartId + 'Labels'], window[chartId + 'Data']);
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>