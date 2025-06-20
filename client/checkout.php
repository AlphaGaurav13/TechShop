<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_email'])) {
  header("Location: login.php");
  exit();
}

include 'partials/database.php';

// Get cart items
$sql = "SELECT c.quantity, p.product_id, p.product_name, p.product_desc, p.product_price, p.product_image
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        WHERE c.user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;
$cart_items = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $item_total = $row['product_price'] * $row['quantity'];
    $subtotal += $item_total;
    $cart_items[] = $row;
  }
}

// Get user address
$address_sql = "SELECT address_line1, address_line2, city, state, postal_code, country 
                FROM users WHERE email = ?";
$address_stmt = $conn->prepare($address_sql);
$address_stmt->bind_param("s", $_SESSION['user_email']);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$user_address = $address_result->fetch_assoc();

// Handle address form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
  $address_line1 = $_POST['address_line1'];
  $address_line2 = $_POST['address_line2'] ?? '';
  $city = $_POST['city'];
  $state = $_POST['state'];
  $postal_code = $_POST['postal_code'];
  $country = $_POST['country'];
  
  $update_sql = "UPDATE users SET 
                address_line1 = ?, 
                address_line2 = ?, 
                city = ?, 
                state = ?, 
                postal_code = ?, 
                country = ? 
                WHERE email = ?";
  $update_stmt = $conn->prepare($update_sql);
  $update_stmt->bind_param("sssssss", 
    $address_line1, 
    $address_line2, 
    $city, 
    $state, 
    $postal_code, 
    $country, 
    $_SESSION['user_email']
  );
  
  if ($update_stmt->execute()) {
    $success_message = "Address updated successfully!";
    // Refresh address data
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();
    $user_address = $address_result->fetch_assoc();
  } else {
    $error_message = "Failed to update address. Please try again.";
  }
}

// Handle place order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  // Validate payment method selected
  if (empty($_POST['payment_method'])) {
    $error_message = "Please select a payment method";
  } else {
    // Start transaction
    $conn->begin_transaction();
    
    try {
      // 1. Create order
      $order_sql = "INSERT INTO orders (user_email, total_amount, payment_method, status) 
                    VALUES (?, ?, ?, 'Processing')";
      $order_stmt = $conn->prepare($order_sql);
      $total = $subtotal + 5; // $5 shipping
      $order_stmt->bind_param("sds", $_SESSION['user_email'], $total, $_POST['payment_method']);
      $order_stmt->execute();
      $order_id = $conn->insert_id;
      
      // 2. Add order items
      $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                         VALUES (?, ?, ?, ?)";
      $order_item_stmt = $conn->prepare($order_item_sql);
      
      foreach ($cart_items as $item) {
        $order_item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['product_price']);
        $order_item_stmt->execute();
      }
      
      // 3. Clear cart
      $clear_cart_sql = "DELETE FROM cart WHERE user_email = ?";
      $clear_cart_stmt = $conn->prepare($clear_cart_sql);
      $clear_cart_stmt->bind_param("s", $_SESSION['user_email']);
      $clear_cart_stmt->execute();
      
      // Commit transaction
      $conn->commit();
      
      // Set success flag for the animation
      $order_success = true;
    } catch (Exception $e) {
      // Rollback on error
      $conn->rollback();
      $error_message = "Order failed: " . $e->getMessage();
    }
  }
}
?>


  


  <div class="max-w-4xl mx-auto pt-24 pb-12 px-4 sm:px-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Checkout</h1>
    
    <?php if (!empty($cart_items)): ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left Column - Order Summary -->
        <div class="md:col-span-2 space-y-6">
          <!-- Address Section -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Shipping Address</h2>
            
            <?php if (!empty($user_address['address_line1'])): ?>
              <div class="mb-4">
                <p class="text-gray-700"><?= htmlspecialchars($user_address['address_line1']) ?></p>
                <?php if (!empty($user_address['address_line2'])): ?>
                  <p class="text-gray-700"><?= htmlspecialchars($user_address['address_line2']) ?></p>
                <?php endif; ?>
                <p class="text-gray-700">
                  <?= htmlspecialchars($user_address['city']) ?>, 
                  <?= htmlspecialchars($user_address['state']) ?> 
                  <?= htmlspecialchars($user_address['postal_code']) ?>
                </p>
                <p class="text-gray-700"><?= htmlspecialchars($user_address['country']) ?></p>
              </div>
              <button id="edit-address-btn" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Edit Address
              </button>
            <?php endif; ?>
            
            <!-- Address Form (hidden if address exists) -->
            <form id="address-form" method="POST" action="index.php?checkout=true" class="<?= empty($user_address['address_line1']) ? '' : 'hidden' ?> mt-4 space-y-4">
              <div>
                <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1*</label>
                <input type="text" id="address_line1" name="address_line1" required
                       value="<?= htmlspecialchars($user_address['address_line1'] ?? '') ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
              </div>
              
              <div>
                <label for="address_line2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2"
                       value="<?= htmlspecialchars($user_address['address_line2'] ?? '') ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700">City*</label>
                  <input type="text" id="city" name="city" required
                         value="<?= htmlspecialchars($user_address['city'] ?? '') ?>"
                         class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700">State/Province*</label>
                  <input type="text" id="state" name="state" required
                         value="<?= htmlspecialchars($user_address['state'] ?? '') ?>"
                         class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code*</label>
                  <input type="text" id="postal_code" name="postal_code" required
                         value="<?= htmlspecialchars($user_address['postal_code'] ?? '') ?>"
                         class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                  <label for="country" class="block text-sm font-medium text-gray-700">Country*</label>
                  <input type="text" id="country" name="country" required
                         value="<?= htmlspecialchars($user_address['country'] ?? '') ?>"
                         class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
              </div>
              
              <div class="pt-2">
                <button type="submit" name="update_address" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium transition">
                  Save Address
                </button>
              </div>
            </form>
          </div>
          
          <!-- Payment Method -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
            
            <form method="POST"  class="space-y-4">
              <div class="flex items-center">
                <input id="credit-card" name="payment_method" type="radio" value="Credit Card" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                <label for="credit-card" class="ml-3 block text-sm font-medium text-gray-700">Credit Card</label>
              </div>
              
              <div class="flex items-center">
                <input id="paypal" name="payment_method" type="radio" value="PayPal" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">PayPal</label>
              </div>
              
              <div class="flex items-center">
                <input id="cash-on-delivery" name="payment_method" type="radio" value="Cash on Delivery" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label for="cash-on-delivery" class="ml-3 block text-sm font-medium text-gray-700">Cash on Delivery</label>
              </div>
              
              <?php if (!empty($user_address['address_line1'])): ?>
                <div class="pt-4">
                  <button type="submit" name="place_order" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md font-medium transition">
                    Place Order
                  </button>
                </div>
              <?php endif; ?>
            </form>
          </div>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="space-y-6">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
            
            <div class="space-y-4">
              <!-- Cart Items -->
              <div class="space-y-3 max-h-64 overflow-y-auto">
                <?php foreach ($cart_items as $item): ?>
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <img src="Admin/productimge/<?= htmlspecialchars($item['product_image']) ?>" 
                           alt="<?= htmlspecialchars($item['product_name']) ?>"
                           class="w-16 h-16 object-contain rounded-md bg-white border border-gray-200 p-1">
                    </div>
                    <div class="ml-4 flex-1">
                      <h3 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['product_name']) ?></h3>
                      <p class="text-sm text-gray-500">Qty: <?= $item['quantity'] ?></p>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-medium text-gray-900">$<?= number_format($item['product_price'] * $item['quantity'], 2) ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <!-- Order Totals -->
              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between text-base font-medium text-gray-900 mb-2">
                  <p>Subtotal</p>
                  <p>$<?= number_format($subtotal, 2) ?></p>
                </div>
                <div class="flex justify-between text-base font-medium text-gray-900 mb-2">
                  <p>Shipping</p>
                  <p>$5.00</p>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 mt-4 pt-4 border-t border-gray-200">
                  <p>Total</p>
                  <p>$<?= number_format($subtotal + 5, 2) ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Empty Cart State -->
      <div class="bg-white rounded-lg shadow-sm p-8 text-center max-w-md mx-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
        <p class="mt-1 text-gray-500">There's nothing to checkout</p>
        <a href="index.php" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Continue Shopping
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Order Success Modal -->
  <?php if (isset($order_success) && $order_success): ?>
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate__animated animate__fadeIn">
      <div class="bg-white rounded-lg p-8 max-w-md mx-4 animate__animated animate__zoomIn">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="mt-3 text-lg font-medium text-gray-900">Order Placed Successfully!</h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Thank you for your purchase. Your order has been received and is being processed.
            </p>
          </div>
          <div class="mt-6">
            <a href="index.php?orders=true" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-blue-900 bg-blue-100 border border-transparent rounded-md hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500">
              View My Orders
            </a>
            <a href="index.php" class="ml-3 inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500">
              Continue Shopping
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <script>
    
      setTimeout(() => {
        document.getElementById('success-modal').classList.add('animate__fadeOut');
        setTimeout(() => {
          document.getElementById('success-modal').remove();
        }, 500);
      }, 5000);
    </script>
  <?php endif; ?>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Toggle address form
      const editAddressBtn = document.getElementById('edit-address-btn');
      if (editAddressBtn) {
        editAddressBtn.addEventListener('click', function() {
          document.getElementById('address-form').classList.toggle('hidden');
        });
      }
      
      // Show error messages as toast
      <?php if (isset($error_message)): ?>
        showToast('<?= addslashes($error_message) ?>', 'error');
      <?php endif; ?>
      
      <?php if (isset($success_message)): ?>
        showToast('<?= addslashes($success_message) ?>', 'success');
      <?php endif; ?>
    });
    
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md text-white font-medium animate__animated animate__fadeInRight ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
      }`;
      toast.textContent = message;
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
      }, 3000);
    }
  </script>
  <script>
document.addEventListener("DOMContentLoaded", function() {
    // Toggle address form
    const editAddressBtn = document.getElementById('edit-address-btn');
    if (editAddressBtn) {
        editAddressBtn.addEventListener('click', function() {
            document.getElementById('address-form').classList.toggle('hidden');
        });
    }
    
    // Show error messages as toast
    <?php if (isset($error_message)): ?>
        showToast('<?= addslashes($error_message) ?>', 'error');
    <?php endif; ?>
    
    <?php if (isset($success_message)): ?>
        showToast('<?= addslashes($success_message) ?>', 'success');
    <?php endif; ?>
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md text-white font-medium animate__animated animate__fadeInRight ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script><script>
document.addEventListener("DOMContentLoaded", function() {
    // Toggle address form
    const editAddressBtn = document.getElementById('edit-address-btn');
    if (editAddressBtn) {
        editAddressBtn.addEventListener('click', function() {
            document.getElementById('address-form').classList.toggle('hidden');
        });
    }
    
    // Show error messages as toast
    <?php if (isset($error_message)): ?>
        showToast('<?= addslashes($error_message) ?>', 'error');
    <?php endif; ?>
    
    <?php if (isset($success_message)): ?>
        showToast('<?= addslashes($success_message) ?>', 'success');
    <?php endif; ?>
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md text-white font-medium animate__animated animate__fadeInRight ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script>
