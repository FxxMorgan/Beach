<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es TI o jefe
if ($_SESSION['rol'] != 'TI' && $_SESSION['rol'] != 'jefe') {
    header('Location: dashboard.php');
    exit();
}

// Check if 'usuario' key exists in the session
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = 'default_user';
}

if (!function_exists('auditoria')) {
    function auditoria($accion) {
        global $conn;
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'default_user';
        $query = "INSERT INTO auditoria (usuario, accion) VALUES ('$usuario', '$accion')";
        $conn->query($query);
    }
}

// Manejar la actualización de usuarios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    $update_query = "UPDATE usuarios SET nombre='$nombre', email='$email', rol='$rol' WHERE id='$id'";
    if ($conn->query($update_query) === TRUE) {
        auditoria("Usuario $id actualizado");
        echo "<script>Swal.fire('Operación Exitosa', 'Usuario actualizado exitosamente', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error', 'Error al actualizar el usuario', 'error');</script>";
    }
}

// Manejar la eliminación de usuarios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['id'];
    $new_user_id = isset($_POST['new_usuario_id']) ? $_POST['new_usuario_id'] : '';

    // Si se seleccionó un nuevo usuario, reasignar los datos en las tablas correspondientes
    if ($new_user_id != '') {
        // Reasignar datos en la tabla 'facturas'
        $reassign_facturas = "UPDATE facturas SET usuario_id='$new_user_id' WHERE usuario_id='$id'";
        if (!$conn->query($reassign_facturas)) {
            echo "<script>Swal.fire('Error', 'Error al reasignar datos en facturas: " . $conn->error . "', 'error');</script>";
            exit();
        }

        // Reasignar datos en la tabla 'gastos'
        $reassign_gastos = "UPDATE gastos SET usuario_id='$new_user_id' WHERE usuario_id='$id'";
        if (!$conn->query($reassign_gastos)) {
            echo "<script>Swal.fire('Error', 'Error al reasignar datos en gastos: " . $conn->error . "', 'error');</script>";
            exit();
        }

        // Reasignar datos en la tabla 'inventarios'
        $reassign_inventarios = "UPDATE inventarios SET usuario_id='$new_user_id' WHERE usuario_id='$id'";
        if (!$conn->query($reassign_inventarios)) {
            echo "<script>Swal.fire('Error', 'Error al reasignar datos en inventarios: " . $conn->error . "', 'error');</script>";
            exit();
        }

        // Reasignar datos en la tabla 'ventas'
        $reassign_ventas = "UPDATE ventas SET usuario_id='$new_user_id' WHERE usuario_id='$id'";
        if (!$conn->query($reassign_ventas)) {
            echo "<script>Swal.fire('Error', 'Error al reasignar datos en ventas: " . $conn->error . "', 'error');</script>";
            exit();
        }

        auditoria("Datos reasignados del usuario $id al usuario $new_user_id");
    }

    // Eliminar el usuario
    $delete_query = "DELETE FROM usuarios WHERE id='$id'";
    if ($conn->query($delete_query) === TRUE) {
        auditoria("Usuario $id eliminado");

        // Muestra la alerta y redirige después de que el usuario la cierre
        echo "<script>
            Swal.fire({
                title: 'Operación Exitosa',
                text: 'Usuario eliminado exitosamente',
                icon: 'success'
            }).then(() => {
                // Usar window.location.href para redirigir a la página de usuarios
                window.location.href = 'administrar_usuarios.php';
            });
        </script>";
        exit();  // Termina la ejecución para evitar que el script continúe ejecutándose
    } else {
        echo "<script>
            Swal.fire('Error', 'Error al eliminar el usuario: " . $conn->error . "', 'error');
        </script>";
        exit();  // Termina la ejecución en caso de error
    }
}

// Filtrar usuarios por la sucursal del 'jefe'
if ($_SESSION['rol'] == 'jefe') {
    $sucursal_id = $_SESSION['sucursal_id'];
    $usuarios_query = "SELECT * FROM usuarios WHERE sucursal_id = '$sucursal_id'";
} else {
    $usuarios_query = "SELECT * FROM usuarios";
}

$usuarios_result = $conn->query($usuarios_query);

if (!function_exists('auditoria')) {
    function auditoria($accion) {
        global $conn;
        $usuario = $_SESSION['usuario'];
        $query = "INSERT INTO auditoria (usuario, accion) VALUES ('$usuario', '$accion')";
        $conn->query($query);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Asegúrate de que SweetAlert2 se cargue antes de cualquier uso -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Administrar Usuarios</h1>
        <button onclick="location.href='dashboard.php'" class="bg-blue-600 text-white p-2 rounded mb-5">Volver al Dashboard</button>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
            <h2 class="text-2xl font-bold mb-5">Usuarios</h2>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Nombre</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Rol</th>
                        <th class="py-3 px-4 border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $usuarios_result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4 border-b"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-4 border-b">
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" class="w-full p-2 border rounded">
                        </td>
                        <td class="py-3 px-4 border-b">
                                <input type="email" name="email" value="<?php echo $row['email']; ?>" class="w-full p-2 border rounded">
                        </td>
                        <td class="py-3 px-4 border-b">
                                <select name="rol" class="w-full p-2 border rounded">
                                    <option value="TI" <?php echo ($row['rol'] == 'TI') ? 'selected' : ''; ?>>TI</option>
                                    <option value="jefe" <?php echo ($row['rol'] == 'jefe') ? 'selected' : ''; ?>>Jefe</option>
                                    <option value="encargado" <?php echo ($row['rol'] == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
                                </select>
                        </td>
                        <td class="py-3 px-4 border-b">
                                <button type="submit" name="update_user" class="bg-blue-600 text-white p-2 rounded">Actualizar</button>
                            </form>
                            <button type="button" class="bg-red-600 text-white p-2 rounded" onclick="confirmDelete(<?php echo $row['id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal de Confirmación -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4">¿Estás seguro de que deseas eliminar este usuario?</h2>
            <div class="flex justify-between">
                <button id="confirm-delete" class="bg-red-600 text-white p-2 rounded">Confirmar</button>
                <button id="cancel-delete" class="bg-gray-400 text-white p-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('delete_user', true);
                    formData.append('id', userId);
                    formData.append('new_usuario_id', '');  // Dejar vacío si no hay reasignación

                    fetch('administrar_usuarios.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.text())
                    .then(responseText => {
                        Swal.fire('Eliminado!', 'El usuario ha sido eliminado.', 'success')
                            .then(() => {
                                location.reload();
                            });
                    }).catch(error => {
                        Swal.fire('Error', 'Hubo un problema al eliminar el usuario.', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
