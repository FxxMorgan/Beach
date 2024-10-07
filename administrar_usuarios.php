<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'beach');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario es jefe o TI
if ($_SESSION['rol'] != 'jefe' && $_SESSION['rol'] != 'TI') {
    header('Location: dashboard.php');
    exit();
}

// Obtener las sucursales para mostrarlas en el formulario
$sucursales = $conn->query("SELECT id, nombre FROM sucursales");

$sucursal_id = $_SESSION['sucursal_id']; // Sucursal seleccionada desde el dashboard

if (isset($_POST['agregar_usuario'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash de la contraseña
    $rol = $_POST['rol'];
    $sucursal_id = $_POST['sucursal_id'];

    // Insertar el nuevo usuario en la base de datos
    $query = "INSERT INTO usuarios (nombre, email, contraseña, rol, sucursal_id) 
              VALUES ('$nombre', '$email', '$password', '$rol', '$sucursal_id')";
    if ($conn->query($query) === TRUE) {
        echo "Usuario agregado exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['editar_usuario'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    // Actualizar el usuario en la base de datos
    $query = "UPDATE usuarios SET nombre='$nombre', email='$email', rol='$rol' WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        echo "Usuario actualizado exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['confirmar_eliminar_usuario'])) {
    $id = $_POST['id'];
    $new_usuario_id = $_POST['new_usuario_id'];

    if (!empty($new_usuario_id)) {
        // Reasignar datos a otro usuario
        $reasignar_facturas_query = "UPDATE facturas SET usuario_id='$new_usuario_id' WHERE usuario_id='$id'";
        $conn->query($reasignar_facturas_query);

        $reasignar_gastos_query = "UPDATE gastos SET usuario_id='$new_usuario_id' WHERE usuario_id='$id'";
        $conn->query($reasignar_gastos_query);

        $reasignar_inventarios_query = "UPDATE inventarios SET usuario_id='$new_usuario_id' WHERE usuario_id='$id'";
        $conn->query($reasignar_inventarios_query);

        $reasignar_ventas_query = "UPDATE ventas SET usuario_id='$new_usuario_id' WHERE usuario_id='$id'";
        $conn->query($reasignar_ventas_query);
    } else {
        // Eliminar las facturas asociadas al usuario
        $eliminar_facturas_query = "DELETE FROM facturas WHERE usuario_id='$id'";
        $conn->query($eliminar_facturas_query);

        // Eliminar los gastos asociados al usuario
        $eliminar_gastos_query = "DELETE FROM gastos WHERE usuario_id='$id'";
        $conn->query($eliminar_gastos_query);

        // Eliminar los inventarios asociados al usuario
        $eliminar_inventarios_query = "DELETE FROM inventarios WHERE usuario_id='$id'";
        $conn->query($eliminar_inventarios_query);

        // Eliminar las ventas asociadas al usuario
        $eliminar_ventas_query = "DELETE FROM ventas WHERE usuario_id='$id'";
        $conn->query($eliminar_ventas_query);
    }
    
    // Eliminar el usuario de la base de datos
    $eliminar_usuario_query = "DELETE FROM usuarios WHERE id='$id'";
    if ($conn->query($eliminar_usuario_query) === TRUE) {
        echo "Usuario eliminado exitosamente";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Obtener los usuarios de la sucursal seleccionada
$usuarios_query = "SELECT * FROM usuarios WHERE sucursal_id='$sucursal_id'";
$usuarios_result = $conn->query($usuarios_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-5">Administrar Usuarios - Sucursal: <?php echo $sucursal_id; ?></h1>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <form method="POST">
                <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                <input type="text" id="nombre" name="nombre" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="email" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" id="email" name="email" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="password" class="block text-gray-700 font-bold mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">

                <label for="rol" class="block text-gray-700 font-bold mb-2">Rol</label>
                <select id="rol" name="rol" required
                        class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <option value="jefe">Jefe</option>
                    <option value="encargado">Encargado</option>
                </select>

                <label for="sucursal_id" class="block text-gray-700 font-bold mb-2">Sucursal</label>
                <select id="sucursal_id" name="sucursal_id" required
                        class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <?php while ($row = $sucursales->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $sucursal_id) ? 'selected' : ''; ?>>
                            <?php echo $row['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="agregar_usuario"
                        class="w-full bg-green-600 text-white p-3 rounded-lg font-bold hover:bg-green-700">Agregar Usuario</button>
            </form>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
            <h2 class="text-2xl font-bold mb-5">Usuarios de la Sucursal</h2>
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
                                    <option value="jefe" <?php echo ($row['rol'] == 'jefe') ? 'selected' : ''; ?>>Jefe</option>
                                    <option value="encargado" <?php echo ($row['rol'] == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
                                </select>
                        </td>
                        <td class="py-3 px-4 border-b">
                                <button type="submit" name="editar_usuario" class="bg-blue-600 text-white p-2 rounded">Actualizar</button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="button" name="eliminar_usuario" class="bg-red-600 text-white p-2 rounded" onclick="confirmDelete(<?php echo $row['id']; ?>)">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Confirmar Eliminación</h2>
            <p class="mb-4">Está a punto de eliminar este usuario. Esto también eliminará toda la información asociada a este usuario. ¿Desea continuar?</p>
            <form method="POST">
                <input type="hidden" id="deleteUserId" name="id">
                <label for="new_usuario_id" class="block text-gray-700 font-bold mb-2">Reasignar datos a otro usuario (opcional)</label>
                <select id="new_usuario_id" name="new_usuario_id" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                    <option value="">Seleccionar usuario</option>
                    <?php while ($row = $usuarios_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="button" class="bg-gray-600 text-white p-2 rounded mr-2" onclick="closeModal()">Cancelar</button>
                <button type="submit" name="confirmar_eliminar_usuario" class="bg-red-600 text-white p-2 rounded">Eliminar</button>
            </form>
        </div>
    </div>

    

    <script>
        function confirmDelete(userId) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }
    </script>
</body>
</html>