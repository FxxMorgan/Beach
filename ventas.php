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
$time_range = $_POST['time_range'] ?? 'month'; // Validación para evitar warnings
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$date_format = '%Y-%m'; // Default es mensual

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
    $sucursal_nombre = $sucursal['nombre'] ?? 'Desconocida'; // Evitar acceso a null
}

// Función de auditoría
function auditoria($conn, $accion, $usuario_id) {
    $fecha = date('Y-m-d H:i:s');
    $usuario_query = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $usuario_query->bind_param('i', $usuario_id);
    $usuario_query->execute();
    $usuario = $usuario_query->get_result()->fetch_assoc();
    $usuario_nombre = $usuario['nombre'] ?? 'Desconocido'; // Evitar acceso a null
    $query = $conn->prepare("INSERT INTO auditoria (usuario_id, usuario_nombre, accion, fecha) VALUES (?, ?, ?, ?)");
    $query->bind_param('isss', $usuario_id, $usuario_nombre, $accion, $fecha);
    $query->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que monto y comentario estén definidos antes de acceder
    $monto = isset($_POST['monto']) ? str_replace('.', '', $_POST['monto']) : 0;
    $comentario = $_POST['comentario'] ?? ''; // Si no está definido, asignar vacío
    $fecha = date('Y-m-d H:i:s');
    
    $insert_query = $conn->prepare("INSERT INTO ventas (fecha, monto, usuario_id, sucursal_id, comentario) VALUES (?, ?, ?, ?, ?)");
    $insert_query->bind_param('sdiss', $fecha, $monto, $usuario_id, $sucursal_id, $comentario);
    if ($insert_query->execute() === TRUE) {
        auditoria($conn, "Venta registrada: Monto: $monto, Comentario: $comentario, Fecha: $fecha, Sucursal ID: $sucursal_id", $usuario_id);
        $success_message = "Venta registrada exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Configuración de formato y condición de fecha según la selección del usuario
$date_condition = '';
switch ($time_range) {
    case 'day':
        $date_format = '%Y-%m-%d';
        break;
    case 'year':
        $date_format = '%Y';
        break;
    case 'custom':
        if ($start_date && $end_date) {
            $date_format = '%Y-%m-%d';
            $date_condition = "AND fecha BETWEEN ? AND ?";
        }
        break;
    case 'month':
    default:
        $date_format = '%Y-%m';
        break;
}

// Obtener ventas y nombres de usuarios
if ($sucursal_id == 'todas') {
    $query = $conn->prepare("SELECT v.*, u.nombre as usuario_nombre, s.nombre as sucursal_nombre FROM ventas v JOIN usuarios u ON v.usuario_id = u.id JOIN sucursales s ON v.sucursal_id = s.id");
} else {
    $query = $conn->prepare("SELECT v.*, u.nombre as usuario_nombre, s.nombre as sucursal_nombre FROM ventas v JOIN usuarios u ON v.usuario_id = u.id JOIN sucursales s ON v.sucursal_id = s.id WHERE v.sucursal_id = ?");
    $query->bind_param('i', $sucursal_id);
}
$query->execute();
$result = $query->get_result();

// Obtener datos para el gráfico
if ($sucursal_id == 'todas') {
    $ventas_query = "SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM(monto) AS total FROM ventas GROUP BY periodo ORDER BY periodo ASC";
} else {
    $ventas_query = "SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM(monto) AS total FROM ventas WHERE sucursal_id = ? GROUP BY periodo ORDER BY periodo ASC";
}

$ventas_stmt = $conn->prepare($ventas_query);
if ($sucursal_id != 'todas') {
    if ($date_condition) {
        $ventas_stmt->bind_param('iss', $sucursal_id, $start_date, $end_date);
    } else {
        $ventas_stmt->bind_param('i', $sucursal_id);
    }
}
$ventas_stmt->execute();
$ventas_result = $ventas_stmt->get_result();
$ventas_data = [];
$ventas_labels = [];
while ($row = $ventas_result->fetch_assoc()) {
    $ventas_labels[] = $row['periodo'];
    $ventas_data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf/notyf.min.css">
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
        <h1 class="text-3xl font-bold text-center mb-5">Ventas - Sucursal: <?php echo $sucursal_nombre; ?></h1>
        
        <?php if ($rol == 'TI'): ?>
        <form method="POST" id="filterForm" class="mb-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="time_range" class="block text-lg font-semibold mb-2">Seleccionar Rango de Tiempo:</label>
                    <select name="time_range" id="time_range" class="border p-2 rounded-md w-full">
                        <option value="day" <?php echo $time_range == 'day' ? 'selected' : ''; ?>>Diario</option>
                        <option value="month" <?php echo $time_range == 'month' ? 'selected' : ''; ?>>Mensual</option>
                        <option value="year" <?php echo $time_range == 'year' ? 'selected' : ''; ?>>Anual</option>
                        <option value="custom" <?php echo $time_range == 'custom' ? 'selected' : ''; ?>>Personalizado</option>
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
            </div>

            <div id="customDates" class="grid grid-cols-1 sm:grid-cols-2 gap-4" style="display: none;">
                <div>
                    <label for="start_date" class="block text-lg font-semibold mb-2">Fecha Inicio:</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="border p-2 rounded-md w-full">
                </div>
                <div>
                    <label for="end_date" class="block text-lg font-semibold mb-2">Fecha Fin:</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="border p-2 rounded-md w-full">
                </div>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Filtrar</button>
        </form>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
            <form method="POST" id="ventaForm" class="mb-6">
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

            <div class="chart-container mx-auto mb-6">
                <canvas id="ventasChart"></canvas>
            </div>

            <table id="ventasTable" class="display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                        <th>Sucursal</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo "$" . number_format($row['monto'], 0, '', '.'); ?></td>
                            <td><?php echo $row['usuario_nombre']; ?></td>
                            <td><?php echo $row['sucursal_nombre']; ?></td>
                            <td><?php echo $row['comentario']; ?></td>
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
        // Mostrar/ocultar inputs de fecha según el rango de tiempo
        $('#time_range').change(function() {
            if ($(this).val() === 'custom') {
                $('#customDates').show();  // Mostrar las fechas si selecciona "personalizado"
            } else {
                $('#customDates').hide();  // Ocultar las fechas si no es "personalizado"
            }
        }).trigger('change');  // Ejecutar la lógica al cargar la página

        // Actualizar automáticamente el formulario cuando se cambie el rango de tiempo o la sucursal
        $('#time_range, #sucursal_id').change(function() {
            $('#filterForm').submit();  // Enviar el formulario automáticamente al cambiar
        });

        // Inicializar DataTables
        $('#ventasTable').DataTable({
            responsive: true
        });

        // Inicializar Notyf para mostrar alertas de éxito
        var notyf = new Notyf();

        <?php if (!empty($success_message)): ?>
            notyf.success('<?php echo $success_message; ?>');  // Mostrar el mensaje de éxito si existe
        <?php endif; ?>

        // Gráfico de ventas
        var ctxVentas = document.getElementById('ventasChart').getContext('2d');
        var ventasChart = new Chart(ctxVentas, {
            type: 'line',  // Tipo de gráfico
            data: {
                labels: <?php echo json_encode($ventas_labels); ?>,  // Etiquetas de los períodos
                datasets: [{
                    label: 'Ventas',
                    data: <?php echo json_encode($ventas_data); ?>,  // Datos de las ventas
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',  // Color de fondo
                    borderColor: 'rgba(54, 162, 235, 1)',  // Color de borde
                    borderWidth: 1  // Ancho de borde
                }]
            },
            options: {
                responsive: true,  // Hacer el gráfico responsivo
                maintainAspectRatio: false,  // Mantener el aspecto del gráfico
                scales: {
                    y: {
                        beginAtZero: true  // Comenzar el eje Y en 0
                    }
                }
            }
        });
    });
</script>

</body>
</html>
