<?php
    session_start();
    $host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "wdd_velvet_vogue_db";

    // Connect to database
    $conn = new mysqli($host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch latest 5 products
    $new_arrivals_query = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 5";
    $new_arrivals_result = mysqli_query($conn, $new_arrivals_query);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title> Velvet Vlogue </title>
        <link rel="stylesheet" href="STYLEE.CSS">
    </head>
    <body >
    <header class="header">
        <div class="logo-box">
                <img class="logo" src="logo.jpg">
                <h2 class="Vogue">Velvet Vogue</h2>
        </div>

        <form method="GET" action="CATEGORIES PAGE.php" class="search-form">
            <input type="text" name="search" placeholder="What do you want?" class="search-bar">
        </form>
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
    <div class="hero-banner">
        <img src="resource images\banner.png" class="banner">
        </div>
        <div class="categories">
    <h2 class="section-ti">SHOP BY CATEGORIES</h2>
    <div class="categories-row">
            <a href="products.php?category=Womens Wear" class="cat-item">
                <img src="resource images\pic1.jpg" alt="Women" class="cat"><br>
                <span>Womens Wear</span>
            </a>

            <a href="products.php?category=Mens Wear" class="cat-item">
                <img src="resource images\pic2.jpg" alt="Men" class="cat"><br>
                <span>Mens Wear</span>
            </a>

            <a href="products.php?category=Kids Wear" class="cat-item">
                <img src="resource images\pic3.jpg" alt="Kids" class="cat"><br>
                <span>Kids Wear</span>
            </a>

            <a href="products.php?category=Accessories" class="cat-item">
                <img src="resource images\pic5.jpg" alt="Accessories" class="cat"><br>
                <span>Accessories</span>
            </a>

            <a href="products.php?category=Footwear" class="cat-item">
                <img src="resource images\pic4.jpg" alt="Footwear" class="cat"><br>
                <span>Footwear</span>
            </a>

        </div>
    </div>

<div class="new-page">
    <h2 class="section-ti">NEW ARRIVALS</h2>
    <div class="new-grid">
        <?php while($row = mysqli_fetch_assoc($new_arrivals_result)): ?>
            <div class="new-card">
                <img src="Admin/<?php echo $row['image']; ?>" alt="<?php echo $row['product_name']; ?>">
                <h3><?php echo $row['product_name']; ?></h3>
                <p class="price">Rs <?php echo number_format($row['price'], 2); ?></p>
                <a href="PRODUCT-DETAILS.php?id=<?php echo $row['product_id']; ?>">
                    <button class="view">View</button>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>


<div class="promotions">
    <h2 class="section-ti">SPECIAL PROMOTIONS</h2>

    <div class="promo-box">
        <h3>Flat 30% OFF on Selected Items</h3>
        <p>Upgrade your wardrobe with our exclusive limited-time offers.</p>
        
    </div>
</div>

    <footer class="footer">

    <div class="footer-container">

        <div class="footer-box">
            <h3>About us </h3>
            <p> Velvet Vogue is a modern fashion brand dedicated to creating timeless styles with premium fabrics. We believe fashion should inspire confidence, comfort, and individuality.  </p>
            <br>
            <h3> Explore  </h3>
            <p> 
                <ul>
                    <li>New Arrivals</li>
                    <li>Trending Collections </li>
                    <li>Lookbooks / Style Guides </li>
                    <li>Designers </li>
                </ul>
            </p>
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
    </body>
</html>
