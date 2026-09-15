<?php
session_start();
if(isset($_SESSION['customer_id'])){
    header("Location: CUSTOMER_PROFILE.php");
    exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Velvet Vogue - Login</title>
    <link rel="stylesheet" href="STYLEE.CSS">
</head>
<body>

<!-- Header same as homepage -->
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

<!-- Login Form -->
<div class="login-container">
    <h2>Sign In</h2>
    <div class="accent-line"></div>

    <form id="loginForm" action="login_process.php" method="post" onsubmit="return validateLoginForm()">
        <label for="email">Email </label>
        <input type="text" id="email" name="email" placeholder="Enter your email or username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>


        <button type="submit" class="login-submit-btn">Login</button>
        <p class="signup-text">Don't have an account? <a href="SINGN_UP.php">Sign Up</a></p>
    </form>
</div>

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
    function validateLoginForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        // Check email format
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Password minimum length
        if (password.length < 6) {
            alert("Password must be at least 6 characters.");
            return false;
        }

        if($row['status'] == 'Blocked'){
        echo "Your account is blocked. Contact admin.";
        exit();
}

        return true; // Form is valid
    }
    </script>
</body>
</html>