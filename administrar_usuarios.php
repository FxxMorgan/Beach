<?php
session_start();
if ($_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener todos los usuarios
$query = "SELECT * FROM usuarios";
$result = $conn->query($query);
?>

<h1>Administrar Usuarios</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Sucursal</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['nombre']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['rol']; ?></td>
        <td><?php echo $row['sucursal_id']; ?></td>
        <td>
            <a href="editar_usuario.php?id=<?php echo $row['id']; ?>">Editar</a> |
            <a href="eliminar_usuario.php?id=<?php echo $row['id']; ?>">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="agregar_usuario.php">Agregar Usuario</a>
