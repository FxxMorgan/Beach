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

// Registro de inventarios solo si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $fecha = date('Y-m-d H:i:s');
    $descripcion = $_POST['descripcion'] ?? '';
    
    $insert_query = $conn->prepare("INSERT INTO inventarios (sku, tipo, cantidad, fecha, usuario_id, sucursal_id, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param('ssidiss', $sku, $tipo, $cantidad, $fecha, $usuario_id, $sucursal_id, $descripcion);
    if ($insert_query->execute() === TRUE) {
        auditoria($conn, "Inventario registrado: SKU: $sku, Tipo: $tipo, Cantidad: $cantidad, Fecha: $fecha, Sucursal ID: $sucursal_id", $usuario_id);
        $success_message = "Inventario registrado exitosamente";
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

// Obtener inventarios y nombres de usuarios
$query_string = "SELECT i.*, u.nombre as usuario_nombre, s.nombre as sucursal_nombre FROM inventarios i JOIN usuarios u ON i.usuario_id = u.id JOIN sucursales s ON i.sucursal_id = s.id WHERE 1=1";

if ($sucursal_id != 'todas') {
    $query_string .= " AND i.sucursal_id = ?";
}

$query = $conn->prepare($query_string);

if ($sucursal_id != 'todas') {
    $query->bind_param('i', $sucursal_id);
}

$query->execute();
$result = $query->get_result();

// Obtener datos para el gráfico
$inventarios_query = "SELECT DATE_FORMAT(fecha, '$date_format') AS periodo, SUM(cantidad) AS total FROM inventarios WHERE 1=1 ";

if ($sucursal_id != 'todas') {
    $inventarios_query .= " AND sucursal_id = ? ";
}

if ($date_condition) {
    $inventarios_query .= " " . $date_condition;  // Solo agregar si se seleccionó rango de fechas personalizado
}

$inventarios_query .= " GROUP BY periodo ORDER BY periodo ASC";

$inventarios_stmt = $conn->prepare($inventarios_query);

// Si se seleccionó un rango de fechas personalizado, agregar parámetros para las fechas
if ($sucursal_id != 'todas' && $date_condition) {
    $inventarios_stmt->bind_param('iss', $sucursal_id, $start_date, $end_date);
} else if ($sucursal_id != 'todas') {
    $inventarios_stmt->bind_param('i', $sucursal_id);
} else if ($date_condition) {
    $inventarios_stmt->bind_param('ss', $start_date, $end_date);
}

$inventarios_stmt->execute();
$inventarios_result = $inventarios_stmt->get_result();
$inventarios_data = [];
$inventarios_labels = [];
while ($row = $inventarios_result->fetch_assoc()) {
    $inventarios_labels[] = $row['periodo'];
    $inventarios_data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Inventarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3.1.0/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3.1.0/notyf.min.js"></script>
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
        <h1 class="text-3xl font-bold text-center mb-5">Inventarios - Sucursal: <?php echo $sucursal_nombre; ?></h1>

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

        <!-- FORMULARIO DE REGISTRO DE INVENTARIOS (método POST) -->
        <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
            <form method="POST" id="inventarioForm" class="mb-6">
                <div class="mb-4">
                    <label for="sku" class="block text-gray-700 font-bold mb-2">SKU</label>
                    <input type="text" id="sku" name="sku" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ingrese SKU">
                </div>
                <div class="mb-4">
                    <label for="tipo" class="block text-gray-700 font-bold mb-2">Tipo</label>
                    <select id="tipo" name="tipo" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="ingreso">Ingreso</option>
                        <option value="retiro">Retiro</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="cantidad" class="block text-gray-700 font-bold mb-2">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" required class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ingrese Cantidad">
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ingrese Descripción">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Registrar Inventario</button>
            </form>

            <div class="chart-container mx-auto mb-6">
                <canvas id="inventariosChart"></canvas>
            </div>

            <table id="inventariosTable" class="display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Sucursal</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['sku']; ?></td>
                            <td><?php echo $row['tipo']; ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['usuario_nombre']; ?></td>
                            <td><?php echo $row['sucursal_nombre']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
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
        var notyf = new Notyf();
        
        // Mostrar/ocultar inputs de fecha según el rango de tiempo
        $('#time_range').change(function() {
            if ($(this).val() === 'custom') {
                $('#customDates').show();
            } else {
                $('#customDates').hide();
            }
        });

        // Inicializar DataTables
        $('#inventariosTable').DataTable({
            responsive: true
        });

        // Gráfico de inventarios
        var ctxInventarios = document.getElementById('inventariosChart').getContext('2d');
        var inventariosChart = new Chart(ctxInventarios, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($inventarios_labels); ?>,
                datasets: [{
                    label: 'Inventarios',
                    data: <?php echo json_encode($inventarios_data); ?>,
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
    });
    </script>
</body>
</html>