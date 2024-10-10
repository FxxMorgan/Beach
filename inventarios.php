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

$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_GET['sucursal_id'] ?? $_SESSION['sucursal_id'];
$rol = $_SESSION['rol'];

// Obtener el nombre de la sucursal desde la base de datos
$sucursal_query = "SELECT nombre FROM sucursales WHERE id='$sucursal_id'";
$sucursal_result = $conn->query($sucursal_query);
$sucursal_nombre = '';
if ($sucursal_result->num_rows > 0) {
    $sucursal_row = $sucursal_result->fetch_assoc();
    $sucursal_nombre = $sucursal_row['nombre'];
}

// Verificar acceso a la sucursal
if ($rol != 'TI' && $sucursal_id != $_SESSION['sucursal_id']) {
    echo "No tienes permisos para acceder a esta sucursal.";
    exit();
}

$query = "SELECT * FROM inventarios WHERE sucursal_id='$sucursal_id'";
$result = $conn->query($query);

// Obtener datos para el gráfico
$inventarios_query = "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(cantidad) AS total FROM inventarios WHERE sucursal_id='$sucursal_id' GROUP BY mes";
$inventarios_result = $conn->query($inventarios_query);
$inventarios_data = [];
$inventarios_labels = [];
while ($row = $inventarios_result->fetch_assoc()) {
    $inventarios_labels[] = $row['mes'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3.1.0/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <form id="inventarioForm" method="POST" class="mb-6">
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
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">Agregar Registro</button>
            </form>
            <div class="chart-container mx-auto mb-6">
                <canvas id="inventariosChart"></canvas>
            </div>
            <table id="inventariosTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Usuario ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $row['tipo']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo $row['usuario_id']; ?></td>
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

    // Inicializar DataTable
    var table = $('#inventariosTable').DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
    });

        // Inicializar el gráfico de inventarios
        var ctxInventarios = document.getElementById('inventariosChart').getContext('2d');
        var inventariosChart = new Chart(ctxInventarios, {
            type: 'bar',
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

        // Búsqueda por SKU
        $('#sku').on('input', function() {
            var sku = $(this).val();
            if (sku.length === 13) { // Asume que el SKU tiene 13 caracteres
                $.getJSON('productos.json', function(data) {
                    var producto = data.find(function(item) {
                        return item.sku === sku;
                    });
                    if (producto) {
                        notyf.success('Nombre: ' + producto.nombre);
                    } else {
                        notyf.error('Producto no encontrado');
                    }
                });
            }
        });

    // Manejo del formulario
    $('#inventarioForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'administrar_inventario.php',
            type: 'POST',
            data: formData,
            dataType: 'json', // Asegura que la respuesta sea tratada como JSON
            success: function(response) {
                if (response.status === 'success') {
                    notyf.success(response.message || 'Registro agregado correctamente');
                    
                    // Agregar nueva fila manualmente a la tabla
                    var newRowData = [
                        response.data.id,           // ID del registro
                        response.data.descripcion,  // Descripción del producto
                        response.data.cantidad,     // Cantidad agregada
                        response.data.tipo,         // Tipo (ingreso/retiro)
                        response.data.fecha,        // Fecha del registro
                        response.data.usuario_id    // ID del usuario
                    ];
                    table.row.add(newRowData).draw(); // Añadir la nueva fila

                } else {
                    notyf.error(response.message || 'Error al agregar el registro');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown); // Log del error
                notyf.error('Hubo un error al agregar el registro');
            }
        });
    });
});
    </script>

</body>
</html>
