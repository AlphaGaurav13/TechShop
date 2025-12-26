<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

?>


<?php
$showAlert = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {


  include 'partials/database.php';
  $username = $_POST["name"];
  $email = $_POST["email"];
  $problem = $_POST["problem"];
  $sql = "INSERT INTO queryt ( name, email, content) VALUES ('$username', '$email', '$problem')";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    echo "<script>alert('your Query is submitted');
    window.location.href = 'index.php?help=true';
    </script>";
  }
}
?>
<div class="flex items-center justify-center h-screen bg-gray-200 pt-20 px-6">
  <div class="p-6 bg-white rounded-2xl shadow-xl w-full max-w-md">
    <div class="text-center">
      <h2 class="mt-4 text-2xl font-semibold text-gray-800">Submit Your Query</h2>
    </div>
    
    <form class="mt-6 space-y-6"  method="post">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
        <input type="text" id="name" name="name" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter your name" required>
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
        <input type="email" id="email" name="email" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter your email" required>
      </div>
      
      <div>
        <label for="problem" class="block text-sm font-medium text-gray-700">Describe Your Problem:</label>
        <textarea id="problem" name="problem" rows="4" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Please provide details of your issue" required></textarea>
      </div>
      
      <button type="submit" class="w-full bg-indigo-600 text-black py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Submit Query
      </button>
    </form>

    <p class="mt-6 text-sm text-gray-600 text-center">We'll get back to you soon!</p>
  </div>
</div>



