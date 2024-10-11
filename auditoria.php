<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es TI
if ($_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

// Obtener los registros de auditoría agrupados por día
$auditoria_query = "SELECT DATE(fecha) as fecha, COUNT(*) as total FROM auditoria GROUP BY DATE(fecha)";
$auditoria_result = $conn->query($auditoria_query);

// Crear arrays para almacenar las fechas y los totales
$fechas = [];
$totales = [];

while ($row = $auditoria_result->fetch_assoc()) {
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
}

// Obtener los registros de auditoría completos
$auditoria_query_all = "SELECT * FROM auditoria";
$auditoria_result_all = $conn->query($auditoria_query_all);

// Contar los registros
$num_rows = $auditoria_result_all->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table.dataTable thead th, table.dataTable thead td {
            padding: 10px 18px;
            border-bottom: 1px solid #111;
        }
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 8px 18px;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #111;
        }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
            padding: 10px 18px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Registros de Auditoría</h1>
        
        <!-- Gráfico de Registros por Día -->
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-2xl font-semibold text-center mb-4">Gráfico de Registros por Día</h2>
            <div class="relative" style="height: 400px;">
                <canvas id="auditoriaChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Tabla de Registros de Auditoría -->
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <?php if ($num_rows > 0): ?>
                <table id="auditoriaTable" class="display responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $auditoria_result_all->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['usuario']; ?></td>
                            <td><?php echo $row['accion']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <script>
                    Swal.fire({
                        icon: 'info',
                        title: 'No hay registros',
                        text: 'Actualmente no hay registros de auditoría disponibles.',
                        confirmButtonText: 'OK'
                    });
                </script>
            <?php endif; ?>
        </div>
        <a href="dashboard.php" class="block mt-4 text-center text-blue-500 hover:underline">Volver al Dashboard</a>
    </div>

    <script>
        $(document).ready(function() {
            $('#auditoriaTable').DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando página _PAGE_ de _PAGES_",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros en total)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }
            });
        });

        var ctx = document.getElementById('auditoriaChart').getContext('2d');
        var auditoriaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($fechas); ?>,
                datasets: [{
                    label: 'Registros por Día',
                    data: <?php echo json_encode($totales); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Registros'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fechas'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>