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

// Obtener lista de sucursales solo para el rol TI
$sucursales = null;
if ($rol == 'TI') {
    $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
    $sucursales_query->execute();
    $sucursales = $sucursales_query->get_result();
}

// Validar y sanitizar el sucursal_id enviado por POST
if (($rol == 'TI' || $rol == 'jefe') && isset($_POST['sucursal_id']) && is_numeric($_POST['sucursal_id'])) {
    $sucursal_id = (int) $_POST['sucursal_id'];
} else if ($rol == 'encargado') {
    // encargado solo puede acceder a su sucursal asignada
    $sucursal_id = $_SESSION['sucursal_id'];
}

// Obtener el nombre de la sucursal seleccionada
$query = $conn->prepare("SELECT nombre FROM sucursales WHERE id = ?");
$query->bind_param('i', $sucursal_id);
$query->execute();
$sucursal = $query->get_result()->fetch_assoc();

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
        $query = $conn->prepare("SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? $date_condition GROUP BY periodo ORDER BY fecha ASC");
        $query->bind_param('iss', $sucursal_id, $start_date, $end_date);
    } else {
        $query = $conn->prepare("SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? GROUP BY periodo ORDER BY fecha ASC");
        $query->bind_param('i', $sucursal_id);
    }
    $query->execute();
    return $query->get_result();
}

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

// Función para obtener datos de todas las sucursales para TI
function get_all_data($conn, $date_format, $date_condition, $start_date, $end_date) {
    $data = [];
    $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
    $sucursales_query->execute();
    $sucursales = $sucursales_query->get_result();

    while ($sucursal = $sucursales->fetch_assoc()) {
        $sucursal_id = $sucursal['id'];
        $sucursal_nombre = $sucursal['nombre'];

        $ventas_result = get_data($conn, 'ventas', $sucursal_id, $date_format, $date_condition, 'monto', $start_date, $end_date);
        $inventarios_result = get_data($conn, 'inventarios', $sucursal_id, $date_format, $date_condition, 'cantidad', $start_date, $end_date);
        $gastos_result = get_data($conn, 'gastos', $sucursal_id, $date_format, $date_condition, 'monto', $start_date, $end_date);

        list($ventas_labels, $ventas_data) = process_data($ventas_result);
        list($inventarios_labels, $inventarios_data) = process_data($inventarios_result);
        list($gastos_labels, $gastos_data) = process_data($gastos_result);

        $data[] = [
            'sucursal' => $sucursal_nombre,
            'ventas' => ['labels' => $ventas_labels, 'data' => $ventas_data],
            'inventarios' => ['labels' => $inventarios_labels, 'data' => $inventarios_data],
            'gastos' => ['labels' => $gastos_labels, 'data' => $gastos_data],
        ];
    }
    return $data;
}

// Obtener datos según el rol del usuario
$data = [];

if ($rol == 'TI') {
    $data = get_all_data($conn, $date_format, $date_condition, $start_date, $end_date);
} else {
    // Función para obtener y procesar los datos de una tabla específica
    function get_processed_data($conn, $sucursal_id, $date_format, $date_condition, $start_date, $end_date, $table, $column) {
        $result = get_data($conn, $table, $sucursal_id, $date_format, $date_condition, $column, $start_date, $end_date);
        return process_data($result);
    }

    // Obtener y procesar datos
    list($ventas_labels, $ventas_data) = get_processed_data($conn, $sucursal_id, $date_format, $date_condition, $start_date, $end_date, 'ventas', 'monto');
    list($inventarios_labels, $inventarios_data) = get_processed_data($conn, $sucursal_id, $date_format, $date_condition, $start_date, $end_date, 'inventarios', 'cantidad');
    list($gastos_labels, $gastos_data) = get_processed_data($conn, $sucursal_id, $date_format, $date_condition, $start_date, $end_date, 'gastos', 'monto');

    $data[] = [
        'sucursal' => $sucursal['nombre'],
        'ventas' => ['labels' => $ventas_labels, 'data' => $ventas_data],
        'inventarios' => ['labels' => $inventarios_labels, 'data' => $inventarios_data],
        'gastos' => ['labels' => $gastos_labels, 'data' => $gastos_data],
    ];
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
            height: 400px;
            width: 100%;
            padding-bottom: 120px;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var timeRangeSelect = document.getElementById('time_range');
        var dateInputs = document.querySelectorAll('input[type="date"]');
        
        function toggleDateInputs() {
            if (timeRangeSelect.value === 'custom') {
                dateInputs.forEach(function(input) {
                    input.style.display = 'block';
                });
            } else {
                dateInputs.forEach(function(input) {
                    input.style.display = 'none';
                    input.value = ''; // Clear date inputs if not custom
                });
            }
        }
        
        timeRangeSelect.addEventListener('change', toggleDateInputs);
        toggleDateInputs(); // Initial call to set correct visibility on page load
    });
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Dashboard - Sucursal: <?php echo isset($sucursal['nombre']) ? $sucursal['nombre'] : 'No seleccionada'; ?></h1>
        <div class="max-w-8xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <nav class="flex flex-wrap justify-center space-x-4 mb-6">
                <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Ventas</a>
                <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Inventarios</a>
                <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Gastos</a>
                <?php if ($rol == 'TI' || $rol == 'jefe'): ?>
                    <a href="administrar_usuarios.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Administrar Usuarios</a>
                    <a href="crear_usuario.php?sucursal_id=<?php echo $sucursal_id; ?>" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Agregar Usuarios</a>
                    <a href="comparativa.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Comparativa</a>
                    <?php if ($rol == 'TI'): ?>
                        <a href="administrar_sucursales.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Administrar Sucursales</a>
                        <a href="auditoria.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mb-2">Auditoría</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <!-- Formulario de selección de rango de tiempo -->
            <form method="POST" class="mb-6 bg-white p-6 rounded-lg shadow-md">
                <label for="time_range" class="block text-lg font-semibold mb-2">Seleccionar Rango de Tiempo:</label>
                <select name="time_range" id="time_range" class="border p-2 rounded-md w-full sm:w-1/3 mb-4">
                    <option value="day" <?php echo $time_range == 'day' ? 'selected' : ''; ?>>Diario</option>
                    <option value="month" <?php echo $time_range == 'month' ? 'selected' : ''; ?>>Mensual</option>
                    <option value="year" <?php echo $time_range == 'year' ? 'selected' : ''; ?>>Anual</option>
                    <option value="custom" <?php echo $time_range == 'custom' ? 'selected' : ''; ?>>Personalizado</option>
                </select>
                <div class="flex flex-col sm:flex-row mb-4">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="border p-2 rounded-md mb-2 sm:mb-0 sm:mr-2" style="display: none;">
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="border p-2 rounded-md" style="display: none;">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Filtrar</button>
            </form>

            <!-- Gráficos -->
            <?php
            $chartCount = 0;
            foreach ($data as $item) {
                $chartCount++;
                $ventasId = 'ventas-' . $chartCount;
                $inventariosId = 'inventarios-' . $chartCount;
                $gastosId = 'gastos-' . $chartCount;
            ?>
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold mb-4"><?php echo $item['sucursal']; ?></h2>

                    <!-- Ventas -->
                    <div class="chart-container">
                        <canvas id="<?php echo $ventasId; ?>"></canvas>
                    </div>
                    <script>
                        var ctxVentas<?php echo $chartCount; ?> = document.getElementById('<?php echo $ventasId; ?>').getContext('2d');
                        new Chart(ctxVentas<?php echo $chartCount; ?>, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($item['ventas']['labels']); ?>,
                                datasets: [{
                                    label: 'Ventas',
                                    data: <?php echo json_encode($item['ventas']['data']); ?>,
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
                    </script>
                    
                    <!-- Inventarios -->
                    <div class="chart-container">
                        <canvas id="<?php echo $inventariosId; ?>"></canvas>
                    </div>
                    <script>
                        var ctxInventarios<?php echo $chartCount; ?> = document.getElementById('<?php echo $inventariosId; ?>').getContext('2d');
                        new Chart(ctxInventarios<?php echo $chartCount; ?>, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($item['inventarios']['labels']); ?>,
                                datasets: [{
                                    label: 'Inventarios',
                                    data: <?php echo json_encode($item['inventarios']['data']); ?>,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
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

                    <!-- Gastos -->
                    <div class="chart-container">
                        <canvas id="<?php echo $gastosId; ?>"></canvas>
                    </div>
                    <script>
                        var ctxGastos<?php echo $chartCount; ?> = document.getElementById('<?php echo $gastosId; ?>').getContext('2d');
                        new Chart(ctxGastos<?php echo $chartCount; ?>, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($item['gastos']['labels']); ?>,
                                datasets: [{
                                    label: 'Gastos',
                                    data: <?php echo json_encode($item['gastos']['data']); ?>,
                                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                    borderColor: 'rgba(255, 159, 64, 1)',
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
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión al final del script
$conn->close();
?>