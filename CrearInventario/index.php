<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notifyjs/3.1.3/notify.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Subir Productos</title>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4">Agregar Productos</h1>
    
    <form id="formProducto" action="cargar.php" method="post">
        <div class="mb-4">
            <label class="block text-sm font-medium">SKU</label>
            <input type="text" name="sku" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Precio</label>
            <input type="number" name="precio" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Descripción</label>
            <textarea name="descripcion" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
        </div>
        <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Agregar Producto</button>
    </form>

    <h2 class="text-xl font-bold mt-6">Cargar Productos desde CSV</h2>
    <form id="formCSV" action="cargar_csv.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <input type="file" name="csv" accept=".csv" required class="mt-1 block w-full" />
        </div>
        <button type="submit" class="mt-4 bg-green-500 text-white py-2 px-4 rounded">Cargar CSV</button>
    </form>

    <script>
        $(document).ready(function() {
            // Código para manejar notificaciones de éxito
            <?php if (isset($_GET['success'])): ?>
                $.notify('Operación realizada con éxito!', 'success');
            <?php endif; ?>
        });
    </script>
</body>
</html>
