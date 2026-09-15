<?php
// Database connection
$host = "localhost";
$user = "root";        //username
$pass = "";            //password
$db = "wdd_velvet_vogue_db";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data and sanitize
$full_name = $conn->real_escape_string($_POST['fullname']);
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm-password'];

// Check if email already exists
$sql_check = "SELECT * FROM customers WHERE email='$email'";
$result = $conn->query($sql_check);
if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered.'); window.history.back();</script>";
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$sql_insert = "INSERT INTO customers (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

if ($conn->query($sql_insert) === TRUE) {
    echo "<script>alert('Account created successfully! Please login.'); window.location.href='SINGN_IN.php';</script>";
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

$conn->close();
?>