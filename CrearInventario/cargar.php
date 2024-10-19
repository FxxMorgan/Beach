<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productosFile = 'productos.json';
    $productos = json_decode(file_get_contents($productosFile), true) ?? [];

    $nuevoProducto = [
        'sku' => $_POST['sku'],
        'nombre' => $_POST['nombre'],
        'precio' => (float)$_POST['precio'],
        'descripcion' => $_POST['descripcion'],
    ];

    $productos[] = $nuevoProducto;
    file_put_contents($productosFile, json_encode($productos, JSON_PRETTY_PRINT));

    header('Location: index.php?success=1');
    exit();
}
?>
