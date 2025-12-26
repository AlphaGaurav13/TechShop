
<div class="bg-gray-100 font-sans text-gray-800 ">
<nav class="bg-gradient-to-r from-gray-200 via-gray-300 to-gray-400 border-b border-gray-300 shadow-md pt-20 px-5">
    <div class="max-w-screen-xl mx-auto">
        <ul class="flex space-x-6 p-4 text-black font-medium justify-center">
            <li><a href="index.php?category=all" class="hover:text-blue-900">Shop All</a></li>
            <li><a href="index.php?category=Computers" class="hover:text-blue-900">Computers</a></li>
            <li><a href="index.php?category=Tablets" class="hover:text-blue-900">Tablets</a></li>
            <li><a href="index.php?category=Drones and camera" class="hover:text-blue-900">Drones & Cameras</a></li>
            <li><a href="index.php?category=Audio" class="hover:text-blue-900">Audio</a></li>
            <li><a href="index.php?category=Mobile" class="hover:text-blue-900">Mobile</a></li>
            <li><a href="index.php?category=Television" class="hover:text-blue-900">TV & Home Cinema</a></li>
            <li><a href="index.php?category=Wearable Tech" class="hover:text-blue-900">Wearable Tech</a></li>
        </ul>
    </div>
</nav>
<?php
include 'partials/database.php';


$category =isset($_GET['category']) ? $_GET['category'] : 'all';

// Prepare SQL query based on category
if ($category === 'all') {
    $query = "SELECT * FROM product";
    $stmt = $conn->prepare($query);
} else {
    $query = "SELECT * FROM product WHERE product_category = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();
?>

    
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8"><?= $category === 'all' ? 'All Products' : htmlspecialchars($category) ?></h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-[5px]"> <!-- Changed to gap-[5px] for all sides -->
        <?php while ($product = $result->fetch_assoc()): ?>
            <a href="./index.php?product_id=<?= htmlspecialchars($product['product_id']) ?>" class="bg-white p-3 shadow rounded relative hover:shadow-lg transition m-[2.5px]"> <!-- Added m-[2.5px] -->
                <div class="relative">
                  
                            <img src="Admin/productimge/<?= htmlspecialchars($product['product_image']) ?>" 
                             alt="<?= htmlspecialchars($product['product_name']) ?>" 
                             class="relative z-0 w-full h-32 object-contain mb-3 transform transition-transform duration-300 hover:scale-105"> <!-- Reduced mb-4 to mb-3 -->
                   
                   
                </div>

                <div class="p-2"> <!-- Added padding container -->
                    <h3 class="text-sm font-semibold line-clamp-2"><?= htmlspecialchars($product['product_name']) ?></h3>
                    <p class="text-gray-600 text-xs mb-1 line-clamp-2"><?= htmlspecialchars($product['product_desc']) ?></p>
                    <div class="flex items-center gap-1 mt-1"> <!-- Price container -->
                        <p class="text-gray-500 line-through text-xs">$<?= number_format($product['product_price'] + 15, 2) ?></p>
                        <p class="text-purple-600 font-bold text-sm">$<?= number_format($product['product_price'], 2) ?></p>
                    </div>
                </div>
                
                <!-- Add to Cart Form -->
                <form action="server/request.php" method="POST" class="absolute bottom-2 right-2">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" name="add_to_cart"
                            class="bg-blue-600 text-white p-1 rounded-full hover:bg-blue-700 transition-colors"
                            onclick="event.stopPropagation()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </form>
            </a>
        <?php endwhile; ?>
    </div>
</div>
</div>