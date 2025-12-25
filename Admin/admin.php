<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use __DIR__ to resolve includes reliably when file is included from other scripts
include_once __DIR__ . '/../partials/database.php';
include_once __DIR__ . '/../partials/commonfiles.php';

// Require login only — allow any logged-in user to access temporarily
if (!isset($_SESSION['user_email'])) {
    echo '<div class="pt-24 px-6 text-center">\n        <h2 class="text-2xl font-semibold mb-4">Please login to access Admin Panel</h2>\n        <a href="../index.php?login=true" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Login</a>\n    </div>';
    return; // stop further rendering of admin panel when not logged in
}
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Product</title>
</head>

<body class="bg-gray-100 ">
    <nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="../index.php?home=true" class="flex items-center space-x-3 rtl:space-x-reverse">

                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"><i class="fa-solid fa-bag-shopping me-2"></i>TechShop</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <a href="../index.php?home=true" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Back to Home</a>

            </div>

        </div>
    </nav>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-md pt-20 px-6 mt-40">


        <h2 class="text-2xl font-bold mb-6 text-center">Add New Product</h2>

        <form action="./Admin/productadd.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product ID</label>
                    <input type="text" name="product_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="product_name" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Product Description</label>
                <textarea name="product_desc" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Price ($)</label>
                    <input type="number" name="product_price" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Category</label>
                    <input type="text" name="product_category" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Additional Info</label>
                <input type="text" name="product_info" class="mt-1 block w-full p-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Product Image</label>
                <input type="file" name="product_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="text-center pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition-all duration-300">
                    Add Product
                </button>
            </div>
        </form>
    </div>
    <?php
    // Database connection already included at top via __DIR__
    $query = "SELECT * FROM product ORDER BY product_id DESC";
    $result = mysqli_query($conn, $query);
?>

<div class="mt-16">
    <h2 class="text-xl font-bold mb-4 text-center">All Products</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl shadow-md">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left">Name</th>
                    <th class="py-3 px-4 text-left">Description</th>
                    <th class="py-3 px-4 text-left">Price</th>
                    <th class="py-3 px-4 text-left">Category</th>
                    <th class="py-3 px-4 text-left">Info</th>
                    <th class="py-3 px-4 text-left">Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4"><?= $row['product_id'] ?></td>
                        <td class="py-2 px-4"><?= $row['product_name'] ?></td>
                        <td class="py-2 px-4"><?= $row['product_desc'] ?></td>
                        <td class="py-2 px-4">$<?= $row['product_price'] ?></td>
                        <td class="py-2 px-4"><?= $row['product_category'] ?></td>
                        <td class="py-2 px-4">
                            <?php if ($row['product_image']) : ?>
                                <img
    src="./uploads/products/<?= htmlspecialchars($row['product_image']) ?>"
    class="h-16 w-16 object-cover rounded-md"
    alt="<?= htmlspecialchars($row['product_name']) ?>"
>
                            <?php else : ?>
                                <span class="text-gray-400">No image</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>

</html>