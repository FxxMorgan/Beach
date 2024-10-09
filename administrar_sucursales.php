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
if ($rol == 'TI' && isset($_POST['sucursal_id']) && is_numeric($_POST['sucursal_id'])) {
    $sucursal_id = (int) $_POST['sucursal_id'];
} else if ($rol == 'jefe' || 'encargado') {
    $sucursal_id = $_SESSION['sucursal_id'];
}

// Obtener datos de todas las sucursales para TI, o datos de una sola sucursal para jefe y encargado
function get_data_by_role($conn, $sucursal_id, $rol, $date_format, $date_condition, $start_date, $end_date) {
    $data = [];

    if ($rol == 'TI') {
        $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
        $sucursales_query->execute();
        $sucursales = $sucursales_query->get_result();
    } else {
        $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales WHERE id = ?");
        $sucursales_query->bind_param("i", $sucursal_id);
        $sucursales_query->execute();
        $sucursales = $sucursales_query->get_result();
    }

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
            'gastos' => ['labels' => $gastos_labels, 'data' => $gastos_data]
        ];
    }

    return $data;
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
                    <a href="crear_usuario.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Agregar Usuarios</a>
                    <a href="administrar_sucursales.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Administrar Sucursales</a>
                    <a href="auditoria.php" class="bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 mt-6 block">Auditoría</a>
                <?php endif; ?>
            </nav>

            <!-- Formulario de selección de rango de tiempo -->
            <form method="POST" class="mb-6 bg-white p-6 rounded-lg shadow-md">
                <label for="time_range" class="block text-lg font-semibold mb-2">Seleccionar Rango de Tiempo:</label>
                <select name="time_range" id="time_range" class="border p-2 rounded-md w-1/3 mb-4">
                    <option value="day" <?php echo $time_range == 'day' ? 'selected' : ''; ?>>Diario</option>
                    <option value="month" <?php echo $time_range == 'month' ? 'selected' : ''; ?>>Mensual</option>
                    <option value="year" <?php echo $time_range == 'year' ? 'selected' : ''; ?>>Anual</option>
                    <option value="custom" <?php echo $time_range == 'custom' ? 'selected' : ''; ?>>Personalizado</option>
                </select>
                <div class="flex mb-4">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="border p-2 rounded-md mr-2">
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="border p-2 rounded-md">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Filtrar</button>
            </form>

            <!-- Gráficos -->
            <?php
            $data = get_data_by_role($conn, $sucursal_id, $rol, $date_format, $date_condition, $start_date, $end_date);
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