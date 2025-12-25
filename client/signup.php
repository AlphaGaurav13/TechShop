<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$showAlert=$userexist=false;
$validusername=$validemail=$validpassword=true;
if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['submit'])) {

    include './partials/database.php';
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];
    
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,19}$/', $username)) {
        $validusername=false;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validemail=false;
    }
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
        $validpassword=false;
    }
    
    if ($password == $confirmpassword && $validusername && $validemail && $validpassword){
        $sql="SELECT * FROM users WHERE email = ?"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $userexist=true;
        }
    }
    
    if ($password == $confirmpassword && $validusername && $validemail && $validpassword && !$userexist) {
        $sql = "INSERT INTO users (username, email, password, confirmpassword, dateandtime) VALUES (?, ?, ?, ?, current_timestamp())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $username, $email, $password, $confirmpassword);
            if ($stmt->execute()) {
                $showAlert = TRUE;
            } else {
                echo "<div class='alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md'><strong>Error:</strong> " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md'><strong>Error:</strong> " . $conn->error . "</div>";
        }
    }
}
?>
<div class="pt-20 px-6">
<?php
if ($userexist) {
    echo '<div class="alert bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>You have already registered!</strong> You can now login 
        <a href="?login=true" class="text-blue-600 font-bold"><i class="fa-solid fa-right-to-bracket"></i></a>.
        <button class="close-alert absolute top-2 right-3 text-green-700 hover:text-red-600">&times;</button>
    </div>';
} else {
    if ($showAlert) {
        echo '<div class="alert bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>Successful!</strong> You can now login 
        <a href="?login=true" class="text-blue-600 font-bold"><i class="fa-solid fa-right-to-bracket"></i></a>.
        <button class="close-alert absolute top-2 right-3 text-green-700 hover:text-red-600">&times;</button>
    </div>';
    }
    if (!$showAlert && isset($_POST['submit'])) {
        echo '<div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>Failed!</strong> Please enter values carefully.
        <button class="close-alert absolute top-2 right-3 text-red-700 hover:text-red-900">&times;</button>
    </div>';
    }
    if (!$validusername) {
        echo '<div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>Failed!</strong> Enter a valid username like <strong>JohnDoe, user_123</strong>.
        <button class="close-alert absolute top-2 right-3 text-red-700 hover:text-red-900">&times;</button>
    </div>';
    }
    if (!$validemail) {
        echo '<div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>Failed!</strong> Enter a valid email like <strong>user@gmail.com</strong>.
        <button class="close-alert absolute top-2 right-3 text-red-700 hover:text-red-900">&times;</button>
    </div>';
    }
    if (!$validpassword) {
        echo '<div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-2 rounded-md relative" role="alert">
        <strong>Failed!</strong> Password must contain (A-Z), (a-z), (0-9), and a special character.
        <button class="close-alert absolute top-2 right-3 text-red-700 hover:text-red-900">&times;</button>
    </div>';
    }
}
?>
</div>
<div class="flex items-center justify-center h-screen bg-gray-200 pt-20 px-6">
<form method="post"  class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">


        <div class="text-center mb-6">
            <i class="fa-solid fa-user-plus text-4xl text-blue-500"></i>
            <h2 class="text-2xl font-bold text-gray-700 mt-2">Sign Up</h2>
        </div>

        <div class="space-y-4">
            <!-- Username Field -->
            <div>
                <label for="username" class="block text-gray-700 font-semibold"><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="Enter username" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-gray-700 font-semibold"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="Enter email" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-gray-700 font-semibold"><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Enter password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirmpassword" class="block text-gray-700 font-semibold"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirmpassword" placeholder="Confirm password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Already Registered? -->
            <p class="text-gray-600 text-sm text-center">Already registered? 
                <a href="?login=true" class="text-blue-600 font-bold"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            </p>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" name="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Sign Up <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Alert Close Button -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".close-alert").forEach(button => {
        button.addEventListener("click", function () {
            let alertBox = this.parentElement;
            alertBox.style.opacity = "0";  // Smooth fade out
            setTimeout(() => alertBox.remove(), 300); // Remove after fade
        });
    });
});
</script>