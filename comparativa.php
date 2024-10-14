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
$error_message = "";

$sucursales = null;
if ($rol == 'TI') {
    $sucursales_query = $conn->prepare("SELECT id, nombre FROM sucursales");
    $sucursales_query->execute();
    $sucursales = $sucursales_query->get_result();
}

$sucursal_nombre = 'Todas';
if ($sucursal_id != 'todas') {
    $sucursal_query = $conn->prepare("SELECT nombre FROM sucursales WHERE id = ?");
    $sucursal_query->bind_param('i', $sucursal_id);
    $sucursal_query->execute();
    $sucursal = $sucursal_query->get_result()->fetch_assoc();
    $sucursal_nombre = $sucursal['nombre'] ?? 'Desconocida';
}

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$comparison_type = $_GET['comparison_type'] ?? 'ventas';

$data_period1 = [];
$data_period2 = [];
$labels = [];

// Validación de fechas en el servidor
if ($start_date && $end_date) {
    if ($start_date > $end_date) {
        $error_message = "La fecha de inicio no puede ser posterior a la fecha final.";
    } else {
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

        // Consulta para el primer período (comparación por meses)
        $query1 = $conn->prepare("SELECT DATE_FORMAT(fecha, '%Y-%m') AS periodo, SUM($column) AS total 
                                  FROM $table 
                                  WHERE sucursal_id = ? AND fecha BETWEEN ? AND ? 
                                  GROUP BY periodo");
        $query1->bind_param('iss', $sucursal_id, $start_date, $end_date);
        $query1->execute();
        $result1 = $query1->get_result();

        while ($row = $result1->fetch_assoc()) {
            $data_period1[] = $row['total'];
            $labels[] = $row['periodo'];
        }

        // Ajustamos el segundo periodo (1 año después)
        $second_start_date = date('Y-m-d', strtotime("$start_date +1 year"));
        $second_end_date = date('Y-m-d', strtotime("$end_date +1 year"));

        $query2 = $conn->prepare("SELECT DATE_FORMAT(fecha, '%Y-%m') AS periodo, SUM($column) AS total 
                                  FROM $table 
                                  WHERE sucursal_id = ? AND fecha BETWEEN ? AND ? 
                                  GROUP BY periodo");
        $query2->bind_param('iss', $sucursal_id, $second_start_date, $second_end_date);
        $query2->execute();
        $result2 = $query2->get_result();

        while ($row = $result2->fetch_assoc()) {
            $data_period2[] = $row['total'];
        }

        if (empty($data_period1)) {
            $data_period1 = [0];
        }
        if (empty($data_period2)) {
            $data_period2 = [0];
        }
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
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if ($error_message): ?>
            <div class="bg-red-500 text-white p-4 rounded-md mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE COMPARACION -->
        <form method="GET" class="mb-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-lg font-semibold mb-2">Fecha de Inicio:</label>
                    <input type="month" name="start_date" id="start_date" class="border p-2 rounded-md w-full" value="<?php echo $start_date; ?>" required>
                </div>
                <div>
                    <label for="end_date" class="block text-lg font-semibold mb-2">Fecha Final:</label>
                    <input type="month" name="end_date" id="end_date" class="border p-2 rounded-md w-full" value="<?php echo $end_date; ?>" required>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="comparison_type" class="block text-lg font-semibold mb-2">Tipo de Comparación:</label>
                    <select name="comparison_type" id="comparison_type" class="border p-2 rounded-md w-full">
                        <option value="ventas" <?php echo ($comparison_type == 'ventas') ? 'selected' : ''; ?>>Ventas</option>
                        <option value="inventarios" <?php echo ($comparison_type == 'inventarios') ? 'selected' : ''; ?>>Inventarios</option>
                        <option value="gastos" <?php echo ($comparison_type == 'gastos') ? 'selected' : ''; ?>>Gastos</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-between items-center mt-4">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">Generar</button>
            </div>
        </form>
        
        <!-- CHART -->
        <canvas id="comparativaChart" width="400" height="200"></canvas>
        <script>
            var ctx = document.getElementById('comparativaChart').getContext('2d');
            var comparativaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Periodo 1: <?php echo $start_date; ?>',
                        data: <?php echo json_encode($data_period1); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Periodo 2: <?php echo $second_start_date; ?>',
                        data: <?php echo json_encode($data_period2); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total'
                            }
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
