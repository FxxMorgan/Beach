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

date_default_timezone_set('America/Santiago');

$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id'];

// Función de auditoría
function auditoria($conn, $accion, $usuario_id) {
    $fecha = date('Y-m-d H:i:s');
    $usuario_query = "SELECT nombre FROM usuarios WHERE id='$usuario_id'";
    $usuario_result = $conn->query($usuario_query);
    $usuario = $usuario_result->fetch_assoc();
    $usuario_nombre = $usuario['nombre'];
    $query = "INSERT INTO auditoria (usuario_id, usuario_nombre, accion, fecha) VALUES ('$usuario_id', '$usuario_nombre', '$accion', '$fecha')";
    $conn->query($query);
}

// Procesar formulario de nuevo gasto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = $_POST['tipo'];
    $monto = str_replace(['.', ','], '', $_POST['monto']);
    $fecha = date('Y-m-d');
    $usuario_id = $_SESSION['usuario_id'];
    $query = "INSERT INTO gastos (tipo, monto, fecha, sucursal_id) VALUES ('$tipo', '$monto', '$fecha', '$sucursal_id')";
    if ($conn->query($query) === TRUE) {
        auditoria($conn, "Gasto agregado: Tipo: $tipo, Monto: $monto, Fecha: $fecha, Sucursal ID: $sucursal_id", $usuario_id);
        echo "<script>
                Swal.fire({
                    title: 'Monto registrado correctamente',
                    text: '¿Deseas ingresar más gastos?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    } else {
                        window.location.href = 'dashboard.php';
                    }
                });
              </script>";
    } else {
        echo "<script>Swal.fire('Error', 'Error: " . $conn->error . "', 'error');</script>";
    }
}

$query = "SELECT * FROM gastos WHERE sucursal_id='$sucursal_id'";
$result = $conn->query($query);

$gastos_query = "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(monto) AS total FROM gastos WHERE sucursal_id='$sucursal_id' GROUP BY mes";
$gastos_result = $conn->query($gastos_query);
$gastos_data = [];
$gastos_labels = [];
while ($row = $gastos_result->fetch_assoc()) {
    $gastos_labels[] = $row['mes'];
    $gastos_data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Gastos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Gastos - Sucursal: <?php echo $sucursal_id; ?></h1>
        <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
            <form method="POST" class="mb-6">
                <div class="mb-4">
                    <label for="tipo" class="block text-gray-700 font-bold mb-2">Descripción del Gasto</label>
                    <select id="tipo" name="tipo" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Internet">Internet</option>
                        <option value="Electricidad">Electricidad</option>
                        <option value="Agua">Agua</option>
                        <option value="Gas">Gas</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="monto" class="block text-gray-700 font-bold mb-2">Monto</label>
                    <input type="text" id="monto" name="monto" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="275.100" oninput="this.value = this.value.replace(/[^0-9,]/g, '');">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Agregar Gasto</button>
            </form>
            <div class="chart-container mx-auto mb-6">
                <canvas id="gastosChart"></canvas>
            </div>
            <table id="gastosTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo isset($row['tipo']) ? $row['tipo'] : 'N/A'; ?></td>
                            <td><?php echo "$" . number_format($row['monto'], 0, '', '.'); ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="mt-6">
                <a href="dashboard.php" class="w-full bg-gray-600 text-white p-3 rounded-lg font-bold hover:bg-gray-700 inline-block text-center">Volver al Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#gastosTable').DataTable();
        });

        var ctxGastos = document.getElementById('gastosChart').getContext('2d');
        var gastosChart = new Chart(ctxGastos, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($gastos_labels); ?>,
                datasets: [{
                    label: 'Gastos',
                    data: <?php echo json_encode($gastos_data); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
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
</body>
</html>