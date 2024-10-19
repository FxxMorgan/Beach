<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $csvFile = $_FILES['csv']['tmp_name'];
    $productosFile = 'productos.json';
    $productos = json_decode(file_get_contents($productosFile), true) ?? [];

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $nuevoProducto = [
                'sku' => $data[0],
                'nombre' => $data[1],
                'precio' => (float)$data[2],
                'descripcion' => $data[3],
            ];
            $productos[] = $nuevoProducto;
        }
        fclose($handle);
    }

    file_put_contents($productosFile, json_encode($productos, JSON_PRETTY_PRINT));

    header('Location: index.php?success=1');
    exit();
}
?>
