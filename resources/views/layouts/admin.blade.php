<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-800 text-white h-screen p-4">
            <h1 class="text-lg font-bold">Admin Panel</h1>
            <ul class="mt-4">
                <li><a href="/products" class="block py-2 hover:bg-gray-700">Products</a></li>
                <li><a href="/categories" class="block py-2 hover:bg-gray-700">Categories</a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="w-3/4 p-6">
            <h2 class="text-2xl font-bold">Welcome to Admin Panel</h2>
            <p>Manage your products and categories here.</p>
        </main>
    </div>
</body>
</html>