<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gaurav</title>
  <?php include './partials/commonfiles.php'; ?>
</head>

<body class="bg-gray-200">
<?php 

include_once './client/header.php';
if(isset($_GET['login'])&&!isset($_SESSION['username'])){
    include ('./client/login.php');
}
elseif(isset($_GET['signup'])&&!isset($_SESSION['username'])){
    include ('./client/signup.php');
}
elseif(isset($_GET['home'])){
    include ('./client/home.php');
}
elseif(isset($_GET['wishlist'])){
    include ('./client/wishlist.php');
}
elseif(isset($_GET['cart'])){
    include ('./client/cart.php');
}
elseif(isset($_GET['orders'])){
    include ('./client/orders.php');
}
elseif(isset($_GET['product_id'])){
    include ('./client/product.php');
}
elseif(isset($_GET['checkout'])){
    include ('./client/checkout.php');
}
elseif(isset($_GET['category'])){
    include ('./client/viewall.php');
}
elseif(isset($_GET['aboutus'])){
    include ('./client/aboutus.php');
}
elseif(isset($_GET['help'])){
    include ('./client/help.php');
}
elseif(isset($_GET['policy'])){
    include ('./client/policy.php');
}
elseif(isset($_GET['order_id'])){
    include ('./client/orderdetails.php');
}
else{
    include ('./client/home.php'); 
}
include_once './client/footer.php';
?>
  </body>
</html>
