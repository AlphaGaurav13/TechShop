<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['logout'])) {
    session_destroy(); 
    header("Location:../index.php?home=true");
    exit();
  } 
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    include '../partials/database.php';
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prevent SQL injection (Important!)
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_email']= $user['email'];
        header('Location: ../index.php?home=true');
        exit(); // Stop execution immediately after redirect
    } else {
        echo '<script>
          alert("Invalid Crentails or You are not registred");
          window.location.href = "../index.php?login=true";
        </script>';
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    include '../partials/database.php';
    
    // Start session to access user data (assuming you have user login system)
    session_start();
    
    $product_id = $_POST['product_id'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);
    $user_email = $_SESSION['user_email'] ?? ''; // Get email from session

    // Validate inputs
    if (!$product_id || $quantity < 1 || !$user_email) {
        die("Invalid request");
    }

    // Insert into cart table with user email
    $query = "INSERT INTO cart (user_email, product_id, quantity) VALUES (?, ?, ?) 
              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $user_email, $product_id, $quantity);
    mysqli_stmt_execute($stmt);
   
    header("Location: ../index.php?product_id=$product_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    include '../partials/database.php';
    
    
    
    $product_id = $_POST['product_id'] ?? '';
    $user_email = $_SESSION['user_email'] ?? '';

    // Validate inputs
    if (!$product_id || !$user_email) {
        header("Location: ../index.php?error=invalid_wishlist_request");
        exit();
    }

    // Check if product already exists in wishlist
    $check_query = "SELECT * FROM wishlist WHERE user_email = ? AND product_id = ?";
$check = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check, "si", $user_email, $product_id); // Assuming user_email is string, product_id is integer
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {
    // Product already in wishlist
    header("Location: ../index.php?product_id=$product_id&wishlist_error=exists");
    exit();
}
    // Insert into wishlist table
    $query = "INSERT INTO wishlist (user_email, product_id, added_at) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $user_email, $product_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../index.php?product_id=$product_id&wishlist_success=true");
    } else {
        header("Location: ../index.php?product_id=$product_id&wishlist_error=true");
    }
    exit();
}

// Handle remove from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_wishlist') {
    include '../partials/database.php';
    session_start();
    
    $product_id = $_POST['product_id'] ?? '';
    $user_email = $_SESSION['user_email'] ?? '';
    $source = $_POST['source'] ?? ''; // Get the source page

    

    $query = "DELETE FROM wishlist WHERE user_email = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $user_email, $product_id);
    
    if (mysqli_stmt_execute($stmt)) {
    
        
        header("Location: ../index.php?wishlist=true");
    } else {
        // Redirect back with error
        header("Location: ../index.php?wishlist=true");
       
       
    }
    exit();
}
//cart item delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && $_POST['action'] === 'remove') {
    include '../partials/database.php';

    $product_id = $_POST['product_id'];

    // Sanitize input
    $product_id = mysqli_real_escape_string($conn, $product_id);

    // Delete item from cart
    $sql = "DELETE FROM cart WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);

    
    header("Location: ../index.php?cart=true");
    exit();
} else {
    // Invalid request
    header("Location: ../index.php?cart=true");
    exit();
}





?>