<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";        //DB username
$pass = "";            //DB password
$db = "wdd_velvet_vogue_db"; //DB name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data and sanitize
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

// Check if user exists
$sql = "SELECT * FROM customers WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Login successful, create session
        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['email'] = $row['email'];
    if($row['status'] == 'Blocked'){
        echo "Your account is blocked. Contact admin.";
        exit();
    }
        echo "<script>alert('Login successful!'); window.location.href='HOME PAGE.php';</script>";
        exit();
    } else {
        echo "<script>alert('Incorrect password.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('No account found with this email.'); window.history.back();</script>";
    exit();
}

$conn->close();
?>