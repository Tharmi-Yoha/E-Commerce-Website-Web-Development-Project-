<?php
// Start session if you want messages
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "wdd_velvet_vogue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Get input and sanitize
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Server-side validation
    if(empty($name) || empty($email) || empty($subject) || empty($message)){
        $_SESSION['error'] = "All fields are required.";
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if($stmt->execute()){
            $_SESSION['success'] = "Message sent successfully!";
        } else {
            $_SESSION['error'] = "Error sending message. Try again later.";
        }
    }

    // Redirect to the same page to refresh form
    header("Location: CONTACT US.php");
    exit();
}
?>