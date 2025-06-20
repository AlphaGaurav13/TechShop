<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'partials/database.php';
$product_id = $_GET['product_id'] ?? 0;
$query = "SELECT * FROM product WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);


if (!$product) {
    echo "<p class='text-center text-red-500'>Product not found.</p>";
    return;
}
?>

<div class="flex items-center justify-center h-screen bg-gray-200 pt-20 px-6">
  <div class="flex flex-col lg:flex-row gap-10 max-w-7xl w-full">

    <!-- Product Image -->
    <div class="flex-1 flex justify-center items-start">
      <img src="<?= $product['product_image'] ? 'Admin/productimge/' . htmlspecialchars($product['product_image']) : 'https://via.placeholder.com/300' ?>" 
           alt="<?= htmlspecialchars($product['product_name']) ?>" 
           class="w-full max-w-sm rounded shadow-md object-contain">
    </div>

    <!-- Product Info -->
    <div class="flex-1 space-y-4">
      <h1 class="text-2xl font-bold"><?= htmlspecialchars($product['product_name']) ?></h1>
      <p class="text-gray-500 text-sm">SKU: <?= htmlspecialchars($product['product_id']) ?></p>
      <p class="text-xl font-semibold text-gray-800">$<?= htmlspecialchars($product['product_price']) ?>.00</p>

      <!-- Quantity Selector -->
      <div>
  <label class="block font-medium mb-1">Quantity</label>
  <div class="flex items-center border w-max rounded">
    <button type="button" id="decrement" class="px-3 py-1">−</button>
    <input id="qty" type="number" value="1" min="1" class="w-12 text-center border-x outline-none" />
    <button type="button" id="increment" class="px-3 py-1">+</button>
  </div>
</div>
<?php if(isset($_SESSION['username'])){?>
    
      <div class="flex flex-col sm:flex-row gap-4 mt-4">
      <form action="server/request.php" method="POST" class="inline">
  <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
  <input type="hidden" name="quantity" id="qty-value" value="1">
  <button type="submit" name="add_to_cart" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition">
    Add to Cart
  </button>
</form>
<form action="server/request.php" method="POST" class="inline">
  <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
  <input type="hidden" name="quantity" id="qty-value" value="1">
<button class="bg-black text-white px-6 py-2 rounded-full hover:bg-gray-800 transition" name="add_to_wishlist"><i class="fa-regular fa-heart"></i> wishlist</button>
</form> 
</div>
<?php } else { ?>
  <div class="flex flex-col sm:flex-row gap-4 mt-4">
      <form action="index.php?login=true" method="POST" class="inline">
  <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
  <input type="hidden" name="quantity" id="qty-value" value="1">
  <button type="submit" name="add_to_cart" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition">
    Add to Cart
  </button>
</form>
<form action="index.php?login=true" method="POST" class="inline">
  <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
  <input type="hidden" name="quantity" id="qty-value" value="1">
<button class="bg-black text-white px-6 py-2 rounded-full hover:bg-gray-800 transition"><i class="fa-regular fa-heart"></i> wishlist</button>
</form> 
</div>
<?php } ?> 

      <!-- Description -->
      <div>
        <p class="text-gray-600 text-sm mt-6">
          <?= (htmlspecialchars($product['product_desc'] ?? "No description available.")) ?>
        </p>
      </div>

      <!-- Accordions -->
      <div class="mt-8 space-y-4">
        <details class="bg-gray-100 rounded p-4">
          <summary class="cursor-pointer font-medium">Product Info</summary>
          <p class="mt-2 text-sm text-gray-600"><?php echo htmlspecialchars($product['product_info']);?> </p>
        </details>

        <details class="bg-gray-100 rounded p-4">
          <summary class="cursor-pointer font-medium">Return & Refund Policy</summary>
          <p class="mt-2 text-sm text-gray-600">Returns within 14 days. Full refund policy here.</p>
        </details>

        <details class="bg-gray-100 rounded p-4">
          <summary class="cursor-pointer font-medium">Shipping Info</summary>
          <p class="mt-2 text-sm text-gray-600">Ships within 2-4 business days. Tracking included.</p>
        </details>
      </div>
    </div>
  </div>
</div>
<script>
  const qtyInput = document.getElementById('qty');
  const qtyValue = document.getElementById('qty-value');
  const incrementBtn = document.getElementById('increment');
  const decrementBtn = document.getElementById('decrement');

  incrementBtn.addEventListener('click', () => {
    qtyInput.value = parseInt(qtyInput.value) + 1;
    qtyValue.value = qtyInput.value;
  });

  decrementBtn.addEventListener('click', () => {
    if (parseInt(qtyInput.value) > 1) {
      qtyInput.value = parseInt(qtyInput.value) - 1;
      qtyValue.value = qtyInput.value;
    }
  });

  qtyInput.addEventListener('input', () => {
    if (parseInt(qtyInput.value) < 1 || isNaN(qtyInput.value)) {
      qtyInput.value = 1;
    }
    qtyValue.value = qtyInput.value;
  });
</script>
