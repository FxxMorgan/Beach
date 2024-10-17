<?php
session_start();

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['sucursal_id']) || !isset($_SESSION['rol'])) {
    header('Location: login.php');
    exit();
}

$yesterday = date('Y-m-d', strtotime('-1 day'));

// Función para obtener la conexión a la base de datos
function get_db_connection() {
    $conn = new mysqli('localhost', 'root', '', 'beach');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    return $conn;
}

$conn = get_db_connection();

// Obtener lista de todas las sucursales
$sucursales_query = $conn->prepare("SELECT id, nombre, tipo FROM sucursales");
$sucursales_query->execute();
$sucursales = $sucursales_query->get_result();

// Función para obtener datos de ventas y gastos del día anterior
function get_sucursal_data($conn, $sucursal_id, $date) {
    // Datos de ventas
    $ventas_query = $conn->prepare("SELECT SUM(monto) AS total_ventas FROM ventas WHERE sucursal_id = ? AND DATE(fecha) = ?");
    $ventas_query->bind_param('is', $sucursal_id, $date);
    $ventas_query->execute();
    $ventas_result = $ventas_query->get_result()->fetch_assoc();
    
    // Datos de gastos
    $gastos_query = $conn->prepare("SELECT SUM(monto) AS total_gastos FROM gastos WHERE sucursal_id = ? AND DATE(fecha) = ?");
    $gastos_query->bind_param('is', $sucursal_id, $date);
    $gastos_query->execute();
    $gastos_result = $gastos_query->get_result()->fetch_assoc();

    return [
        'ventas' => $ventas_result['total_ventas'] ?? 0,
        'gastos' => $gastos_result['total_gastos'] ?? 0,
    ];
}

// Agrupar sucursales por tipo (rubro)
$grouped_data = [];
while ($sucursal = $sucursales->fetch_assoc()) {
    $sucursal_data = get_sucursal_data($conn, $sucursal['id'], $yesterday);
    $grouped_data[$sucursal['tipo']][] = [
        'sucursal' => $sucursal['nombre'],
        'ventas' => $sucursal_data['ventas'],
        'gastos' => $sucursal_data['gastos']
    ];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Sucursales - Datos del día anterior (<?php echo date('d/m/Y', strtotime($yesterday)); ?>)</h1>
        
        <!-- Recorrer los rubros -->
        <?php foreach ($grouped_data as $tipo => $sucursales): ?>
            <h2 class="text-2xl font-bold mb-5"><?php echo $tipo; ?> - Sucursales</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                <?php
                $chartCount = 0;
                foreach ($sucursales as $item) {
                    $chartCount++;
                    $chartId = 'chart-' . $tipo . '-' . $chartCount; // Un ID único para cada gráfico
                ?>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4"><?php echo $item['sucursal']; ?></h3>
                        <p class="text-lg">Ventas: <strong>$<?php echo number_format($item['ventas'], 2); ?></strong></p>
                        <p class="text-lg">Gastos: <strong>$<?php echo number_format($item['gastos'], 2); ?></strong></p>
                        
                        <!-- Gráfico -->
                        <div class="chart-container mt-4" style="position: relative; height: 250px; width: 100%;">
                            <canvas id="<?php echo $chartId; ?>"></canvas>
                        </div>
                        <script>
                            var ctx<?php echo $chartCount; ?> = document.getElementById('<?php echo $chartId; ?>').getContext('2d');
                            new Chart(ctx<?php echo $chartCount; ?>, {
                                type: 'bar',
                                data: {
                                    labels: ['Ventas', 'Gastos'],
                                    datasets: [{
                                        label: 'Datos del día anterior',
                                        data: [<?php echo $item['ventas']; ?>, <?php echo $item['gastos']; ?>],
                                        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                                        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
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
        <?php endforeach; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
