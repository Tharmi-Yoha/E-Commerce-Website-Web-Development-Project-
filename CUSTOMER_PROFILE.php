<?php
session_start();
if(!isset($_SESSION['customer_id'])){
    header("Location: SINGN_IN.php");
    exit();
}

// Connect to database
$host = "localhost";  //DB host
$user = "root";       //DB user
$pass = "";           //DB password
$dbname = "wdd_velvet_vogue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Get current user info
$customer_id = $_SESSION['customer_id'];
$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $full_name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Hash password if changed
    if(!empty($password)){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE customers SET full_name=?, email=?, password=?, phone=?, address=? WHERE customer_id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssssi", $full_name, $email, $password, $phone, $address, $customer_id);
    } else {
        $sql_update = "UPDATE customers SET full_name=?, email=?, phone=?, address=? WHERE customer_id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $customer_id);
    }

    if($stmt->execute()){
        echo "<p style='color:green;'>Profile updated successfully!</p>";
        // Refresh to show updated info
        header("Refresh:0");
    } else {
        echo "<p style='color:red;'>Update failed. Try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products | Velvet Vogue</title>
    <link rel="stylesheet" href="STYLEE.CSS">
</head>
<body>
<!-- Header -->
    <header class="header">
        <div class="logo-box">
                <img class="logo" src="logo.jpg">
                <h2 class="Vogue">Velvet Vogue</h2>
        </div>

        <input type="text" placeholder=" What do you Want? " class="search-bar">
            <nav class="nav-links">
                <a href="HOME PAGE.php">Home</a>
                <a href="CATEGORIES PAGE.php">Products</a>
                <a href="SHOPPING CART.php"> Cart</a>
                <a href="ABOUT US.php"> About us</a>
                <a href="CONTACT US.php"> Contact us</a>
            </nav>
            <?php if(isset($_SESSION['customer_id'])): ?>
                <a href="CUSTOMER_PROFILE.php" class="login-btn">Profile</a>
            <?php else: ?>
                <a href="SINGN_IN.php" class="login-btn">Login</a>
            <?php endif; ?>
    </header>

   <!-- ===== PROFILE SECTION ===== -->

    <section class="profile-section">

        <div class="profile-container">
            <!-- Edit Profile Form -->
            <div class="profile-form">
                <h2>Edit Profile</h2>

                <form method="post">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter new password to change">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                    </div>

                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="logout.php" class="logout-btn" >Logout</a>
                </form>
            </div>

        </div>

    </section>

        <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-box">
                <h3>About us </h3>
                <p> Velvet Vogue is a modern fashion brand dedicated to creating timeless styles with premium fabrics. We believe fashion should inspire confidence, comfort, and individuality.  </p>
                <br>
                <h3> Explore  </h3>
                <ul>
                    <li>New Arrivals</li>
                    <li>Trending Collections </li>
                    <li>Lookbooks / Style Guides </li>
                    <li>Designers </li>
                </ul>
            </div>
            <div class="footer-box">
                <h4>Quick Links</h4>
                <a href="HOMEPAGE.html">Home</a>
                <a href="">Products</a>
                <a href="ABOUT US.HTML">About Us</a>
                <a href="CONTACT US.HTML">Contact</a>
            </div>
            <div class="footer-box">
                <h4>Customer Support</h4>
                <a href="#">Shipping Policy</a>
                <a href="#">Returns</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
            </div>
            <div class="footer-box">
                <h4> Don't miss out </h4>
                <input type="email" placeholder="Enter your email" class="newsletter-input">
                <button class="newsletter-btn">Subscribe</button>
                <h4 style="margin-top: 20px;">Follow Us</h4>
                <div class="social-icons">
                    <a href="#"><img src="fb.png.jpg" alt="Facebook" class="social"></a>
                    <a href="#"><img src="ins.avif" alt="Instagram" class="social"></a>
                    <a href="#"><img src="twi.webp" alt="Twitter" class="social"></a>
                    <a href="#"><img src="pri.avif" alt="Pinterest" class="social"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 Velvet Vogue. All Rights Reserved.
        </div>
    </footer>

    <script>
    document.querySelector(".profile-form form").addEventListener("submit", function(e){
        const fullname = document.querySelector('[name="fullname"]').value.trim();
        const email = document.querySelector('[name="email"]').value.trim();
        const phone = document.querySelector('[name="phone"]').value.trim();

        if(fullname === "" || email === ""){
            alert("Full Name and Email are required.");
            e.preventDefault();
            return;
        }

        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if(!email.match(emailPattern)){
            alert("Enter a valid email.");
            e.preventDefault();
        }

        if(phone !== "" && !/^\+?\d{7,15}$/.test(phone)){
            alert("Enter a valid phone number.");
            e.preventDefault();
        }
    });
    </script>
    </body>
    </html>