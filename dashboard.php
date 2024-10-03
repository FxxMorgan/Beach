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
$sucursal_id = $_SESSION['sucursal_id'];  // Sucursal por defecto
$rol = $_SESSION['rol'];

// Si es jefe o TI, puede seleccionar cualquier sucursal
if (($rol == 'jefe' || $rol == 'TI') && isset($_POST['sucursal_id'])) {
    $sucursal_id = $_POST['sucursal_id'];  // Sucursal seleccionada
}

// Obtener el nombre de la sucursal seleccionada
$query = "SELECT nombre FROM sucursales WHERE id='$sucursal_id'";
$sucursal = $conn->query($query)->fetch_assoc();

// Obtener todas las sucursales (solo para jefes y TI)
if ($rol == 'jefe' || $rol == 'TI') {
    $sucursales_query = "SELECT id, nombre FROM sucursales";
    $sucursales = $conn->query($sucursales_query);
}
?>

<h1>Dashboard - Sucursal: <?php echo $sucursal['nombre']; ?></h1>

<?php if ($rol == 'TI'): ?>
    <h2>Bienvenido, TI</h2>
    <p>Acceso total a todas las sucursales y funcionalidades.</p>

    <!-- Formulario de selección de sucursal -->
    <form method="POST">
        <select name="sucursal_id">
            <?php while ($row = $sucursales->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $sucursal_id) ? 'selected' : ''; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Ver sucursal</button>
    </form>
    <hr>

    <!-- Opciones para TI -->
    <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Ventas</a> |
    <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Inventarios</a> |
    <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Gastos</a> |
    <a href="administrar_usuarios.php">Administrar Usuarios</a> |
    <a href="administrar_sucursales.php">Administrar Sucursales</a>

<?php elseif ($rol == 'jefe'): ?>
    <h2>Bienvenido, Jefe</h2>
    <p>Acceso a todas las sucursales (excepto auditoría).</p>

    <!-- Formulario de selección de sucursal -->
    <form method="POST">
        <select name="sucursal_id">
            <?php while ($row = $sucursales->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $sucursal_id) ? 'selected' : ''; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Ver sucursal</button>
    </form>
    <hr>

    <!-- Opciones para jefes -->
    <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Ventas</a> |
    <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Inventarios</a> |
    <a href="gastos.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Gastos</a>

<?php elseif ($rol == 'encargado'): ?>
    <h2>Bienvenido, Encargado</h2>
    <p>Acceso a la sucursal: <?php echo $sucursal['nombre']; ?></p>
    <a href="ventas.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Ventas</a> |
    <a href="inventarios.php?sucursal_id=<?php echo $sucursal_id; ?>">Ver Inventarios</a>

<?php else: ?>
    <p>No tienes permisos para acceder a esta sección.</p>
<?php endif; ?>
