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
$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id']; // Usamos GET para filtrar
$rol = $_SESSION['rol'];
$success_message = "";
$time_range = $_GET['time_range'] ?? 'month'; // Filtrar por GET
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
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

// Registro de gastos solo si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['monto'])) {
    $gasto_tipo = $_POST['gasto_tipo'] ?? 'fijo'; // Nuevo campo para el tipo de gasto
    $tipo = $_POST['tipo'] ?? ($_POST['descripcion'] ?? ''); // Si es variable, usar la descripción
    $monto = isset($_POST['monto']) ? str_replace(['.', ','], '', $_POST['monto']) : 0;
    $fecha = date('Y-m-d');
    
    $insert_query = $conn->prepare("INSERT INTO gastos (gasto_tipo, tipo, monto, fecha, usuario_id, sucursal_id) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param('sssiii', $gasto_tipo, $tipo, $monto, $fecha, $usuario_id, $sucursal_id);
    if ($insert_query->execute() === TRUE) {
        auditoria($conn, "Gasto registrado: Tipo: $tipo, Monto: $monto, Fecha: $fecha, Sucursal ID: $sucursal_id", $usuario_id);
        $success_message = "Gasto registrado exitosamente";
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

// Obtener gastos y nombres de usuarios
$query_string = "SELECT g.*, u.nombre as usuario_nombre, s.nombre as sucursal_nombre FROM gastos g JOIN usuarios u ON g.usuario_id = u.id JOIN sucursales s ON g.sucursal_id = s.id WHERE 1=1";

if ($sucursal_id != 'todas') {
    $query_string .= " AND g.sucursal_id = ?";
}

$query = $conn->prepare($query_string);

if ($sucursal_id != 'todas') {
    $query->bind_param('i', $sucursal_id);
}

$query->execute();
$result = $query->get_result();

// Obtener datos para el gráfico
$gastos_query = "SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM(monto) AS total FROM gastos WHERE 1=1 ";

if ($sucursal_id != 'todas') {
    $gastos_query .= " AND sucursal_id = ? ";
}

if ($date_condition) {
    $gastos_query .= " " . $date_condition;  // Solo agregar si se seleccionó rango de fechas personalizado
}

$gastos_query .= " GROUP BY periodo ORDER BY periodo ASC";

$gastos_stmt = $conn->prepare($gastos_query);

// Si se seleccionó un rango de fechas personalizado, agregar parámetros para las fechas
if ($sucursal_id != 'todas' && $date_condition) {
    $gastos_stmt->bind_param('iss', $sucursal_id, $start_date, $end_date);
} else if ($sucursal_id != 'todas') {
    $gastos_stmt->bind_param('i', $sucursal_id);
} else if ($date_condition) {
    $gastos_stmt->bind_param('ss', $start_date, $end_date);
}

$gastos_stmt->execute();
$gastos_result = $gastos_stmt->get_result();
$gastos_data = [];
$gastos_labels = [];
while ($row = $gastos_result->fetch_assoc()) {
    $gastos_labels[] = $row['periodo'];
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
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
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
        <h1 class="text-3xl font-bold text-center mb-5">Gastos - Sucursal: <?php echo $sucursal_nombre; ?></h1>

        <!-- FORMULARIO DE FILTRO (Rango de tiempo y sucursal) -->
        <?php if ($rol == 'TI'): ?>
        <form method="GET" id="filterForm" class="mb-6 space-y-4">
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
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Filtrar</button>
        </form>
        <?php endif; ?>

        <!-- FORMULARIO DE REGISTRO DE GASTOS (método POST) -->
        <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
            <form method="POST" class="mb-6">
                <div class="mb-4">
                    <label for="gasto_tipo" class="block text-gray-700 font-bold mb-2">Tipo de Gasto</label>
                    <select id="gasto_tipo" name="gasto_tipo" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="fijo">Gasto Fijo</option>
                        <option value="variable">Gasto Variable</option>
                    </select>
                </div>
                <div class="mb-4" id="description_container">
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

            <table id="gastosTable" class="display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Sucursal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['gasto_tipo']; ?></td>
                            <td><?php echo $row['tipo']; ?></td>
                            <td><?php echo "$" . number_format($row['monto'], 0, '', '.'); ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['usuario_nombre']; ?></td>
                            <td><?php echo $row['sucursal_nombre']; ?></td>
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
                $('#customDates').show();
            } else {
                $('#customDates').hide();
            }
        });

        // Inicializar DataTables
        $('#gastosTable').DataTable({
            responsive: true
        });

        // Gráfico de gastos
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

        // Cambiar formulario según tipo de gasto
        $('#gasto_tipo').change(function() {
            var tipoGasto = $(this).val();
            if (tipoGasto === 'variable') {
                $('#description_container').html(`
                    <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción del Gasto</label>
                    <input type="text" id="descripcion" name="descripcion" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Descripción del gasto">
                `);
            } else {
                $('#description_container').html(`
                    <label for="tipo" class="block text-gray-700 font-bold mb-2">Descripción del Gasto</label>
                    <select id="tipo" name="tipo" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Internet">Internet</option>
                        <option value="Electricidad">Electricidad</option>
                        <option value="Agua">Agua</option>
                        <option value="Gas">Gas</option>
                    </select>
                `);
            }
        });
    });
    </script>
</body>
</html>