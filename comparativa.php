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

date_default_timezone_set('America/Santiago');

$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id'];
$rol = $_SESSION['rol'];
$success_message = "";

// Obtener lista de sucursales solo para el rol TI
$sucursales = null;
if ($rol == 'TI') {
    $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
    $sucursales_query->execute();
    $sucursales = $sucursales_query->get_result();
}

// Obtener el nombre de la sucursal
$sucursal_nombre = 'Todas';
if ($sucursal_id != 'todas') {
    $sucursal_query = $conn->prepare("SELECT nombre FROM sucursales WHERE id = ?");
    $sucursal_query->bind_param('i', $sucursal_id);
    $sucursal_query->execute();
    $sucursal = $sucursal_query->get_result()->fetch_assoc();
    $sucursal_nombre = $sucursal['nombre'] ?? 'Desconocida';
}

// Obtener datos para los dos periodos a comparar
$date1 = $_GET['date1'] ?? '';
$date2 = $_GET['date2'] ?? '';
$comparison_type = $_GET['comparison_type'] ?? 'ventas';
$data_period1 = [];
$data_period2 = [];

if ($date1 && $date2) {
    switch ($comparison_type) {
        case 'inventarios':
            $table = 'inventarios';
            $column = 'cantidad';
            break;
        case 'gastos':
            $table = 'gastos';
            $column = 'monto';
            break;
        case 'ventas':
        default:
            $table = 'ventas';
            $column = 'monto';
            break;
    }

    $query1 = $conn->prepare("SELECT DATE_FORMAT(fecha, '%Y-%m-%d') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? AND DATE_FORMAT(fecha, '%Y-%m-%d') = ? GROUP BY periodo");
    $query1->bind_param('is', $sucursal_id, $date1);
    $query1->execute();
    $result1 = $query1->get_result();
    while ($row = $result1->fetch_assoc()) {
        $data_period1[] = $row['total'];
    }

    $query2 = $conn->prepare("SELECT DATE_FORMAT(fecha, '%Y-%m-%d') AS periodo, SUM($column) AS total FROM $table WHERE sucursal_id = ? AND DATE_FORMAT(fecha, '%Y-%m-%d') = ? GROUP BY periodo");
    $query2->bind_param('is', $sucursal_id, $date2);
    $query2->execute();
    $result2 = $query2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $data_period2[] = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparativa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Comparativa - Sucursal: <?php echo $sucursal_nombre; ?></h1>
        
        <!-- FORMULARIO DE COMPARACION -->
        <form method="GET" class="mb-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date1" class="block text-lg font-semibold mb-2">Fecha 1:</label>
                    <input type="date" name="date1" id="date1" class="border p-2 rounded-md w-full" value="<?php echo $date1; ?>" required>
                </div>
                <div>
                    <label for="date2" class="block text-lg font-semibold mb-2">Fecha 2:</label>
                    <input type="date" name="date2" id="date2" class="border p-2 rounded-md w-full" value="<?php echo $date2; ?>" required>
                </div>
            </div>
            <div>
                <label for="comparison_type" class="block text-lg font-semibold mb-2">Tipo de Comparación:</label>
                <select name="comparison_type" id="comparison_type" class="border p-2 rounded-md w-full">
                    <option value="ventas" <?php echo $comparison_type == 'ventas' ? 'selected' : ''; ?>>Ventas</option>
                    <option value="inventarios" <?php echo $comparison_type == 'inventarios' ? 'selected' : ''; ?>>Inventarios</option>
                    <option value="gastos" <?php echo $comparison_type == 'gastos' ? 'selected' : ''; ?>>Gastos</option>
                </select>
            </div>
            <div>
                <label for="sucursal_id" class="block text-lg font-semibold mb-2">Seleccionar Sucursal:</label>
                <select name="sucursal_id" id="sucursal_id" class="border p-2 rounded-md w-full">
                    <option value="todas">Todas</option>
                    <?php while ($row = $sucursales->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $sucursal_id ? 'selected' : ''; ?>><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Comparar</button>
        </form>

        <div class="chart-container mx-auto mb-6">
            <canvas id="comparativaChart"></canvas>
        </div>
        
        <a href="dashboard.php" class="w-full bg-gray-600 text-white p-3 rounded-lg font-bold hover:bg-gray-700 inline-block text-center">Volver al Dashboard</a>
    </div>

    <script>
var ctx = document.getElementById('comparativaChart').getContext('2d');
var comparativaChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['<?php echo $period1; ?>', '<?php echo $period2; ?>'],
        datasets: [{
            label: 'Period 1: <?php echo $period1; ?>',
            data: [<?php echo json_encode($data_period1); ?>, 0],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Period 2: <?php echo $period2; ?>',
            data: [0, <?php echo json_encode($data_period2); ?>],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            x: {
                stacked: true,
                title: {
                    display: true,
                    text: 'Periodos'
                },
                ticks: {
                    autoSkip: false
                },
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Ventas Totales'
                }
            }
        },
        barThickness: 200,
        barPercentage: 0.5,
        categoryPercentage: 0.5
    }
});
</script>

</body>
</html>