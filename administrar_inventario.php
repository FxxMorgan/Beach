<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['sucursal_id'])) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'beach');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Validar y limpiar entradas
$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_SESSION['sucursal_id'];
$sku = isset($_POST['sku']) ? $conn->real_escape_string(trim($_POST['sku'])) : '';
$tipo = isset($_POST['tipo']) ? $conn->real_escape_string(trim($_POST['tipo'])) : '';
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$fecha = date('Y-m-d H:i:s');

// Verificar que todos los datos requeridos están presentes
if (empty($sku) || empty($tipo) || $cantidad <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos o inválidos']);
    exit();
}

// Leer productos desde el archivo JSON
$productosData = json_decode(file_get_contents('productos.json'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Error al leer el archivo JSON']);
    exit();
}

// Buscar el producto por SKU
$producto = array_values(array_filter($productosData, function($item) use ($sku) {
    return $item['sku'] === $sku;
}));

// Verificar si el producto existe
if (empty($producto)) {
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
    exit();
}

$descripcion = $producto[0]['nombre'];

// Insertar el registro en la base de datos usando consultas preparadas
$query = $conn->prepare("INSERT INTO inventarios (descripcion, cantidad, tipo, fecha, usuario_id, sucursal_id) VALUES (?, ?, ?, ?, ?, ?)");
$query->bind_param("sissii", $descripcion, $cantidad, $tipo, $fecha, $usuario_id, $sucursal_id);

if ($query->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $query->error]);
}

// Cerrar conexión
$query->close();
$conn->close();
?>
