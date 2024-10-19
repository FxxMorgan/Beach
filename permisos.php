<?php
session_start();
if ($_SESSION['rol'] != 'TI') {
    die("Access denied");
}

$conn = new mysqli('localhost', 'root', '', 'beach');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_id = $_POST['role_id'];
    $permissions = json_decode($_POST['permissions'], true);
    $conn->query("DELETE FROM permissions WHERE role_id = $role_id");
    foreach ($permissions as $permission) {
        $stmt = $conn->prepare("INSERT INTO permissions (role_id, permission) VALUES (?, ?)");
        $stmt->bind_param("is", $role_id, $permission);
        $stmt->execute();
    }
}

$roles_result = $conn->query("SELECT * FROM roles");
$permissions_result = $conn->query("SELECT * FROM permisos");
$roles_permissions = [];
while ($row = $permissions_result->fetch_assoc()) {
    $roles_permissions[$row['role_id']][] = $row['permission'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Manage Permissions</h1>
        <form method="POST" class="space-y-4">
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role_id" id="role_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php while ($role = $roles_result->fetch_assoc()): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="permissions" class="block text-sm font-medium text-gray-700">Permissions</label>
                <input type="text" name="permissions" id="permissions" placeholder="Comma separated permissions" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700">Update Permissions</button>
            </div>
        </form>
    </div>
</body>
</html>