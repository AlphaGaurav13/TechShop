<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../partials/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name     = $_POST['product_name'] ?? '';
    $product_desc     = $_POST['product_desc'] ?? '';
    $product_price    = $_POST['product_price'] ?? '';
    $product_info     = $_POST['product_info'] ?? '';
    $product_category = $_POST['product_category'] ?? '';

    // Validate inputs
    if (empty($product_name) || empty($product_price) || empty($product_category)) {
        die('Error: Product name, price, and category are required.');
    }

    // IMAGE UPLOAD
    $target_dir = __DIR__ . "/../uploads/products/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = '';
    if (!empty($_FILES["product_image"]["name"])) {
        $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            echo 'Error uploading image.';
            exit;
        }
    }

    // INSERT QUERY (NO product_id, NO product_info)
    $sql = "INSERT INTO product 
        (product_name, product_desc, product_price, product_category, product_image)
        VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param(
        "ssiss",
        $product_name,
        $product_desc,
        $product_price,
        $product_category,
        $image_name
    );

    if ($stmt->execute()) {
        header("Location: ../index.php?admin=true");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
