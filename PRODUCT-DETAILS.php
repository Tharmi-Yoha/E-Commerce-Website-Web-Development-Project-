<?php
    session_start();
    $host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "wdd_velvet_vogue_db";

    $conn = new mysqli($host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    // Get product ID from URL
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Fetch product
    $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$product_id");
    $product = mysqli_fetch_assoc($result);

    if(!$product){
        echo "Product not found!";
        exit();
    }

    // Convert color and size CSV to arrays
    $colors = explode(',', $product['color']); // e.g., "Red,Blue,Black"
    $sizes  = explode(',', $product['size']);  // e.g., "S,M,L,XL"

    // Handle Add to Cart
    if(isset($_POST['add_to_cart'])){
        if(!isset($_SESSION['customer_id'])){
            echo "<script>alert('Please login first');</script>";
        } else {
            $selected_color = $_POST['color'];
            $selected_size  = $_POST['size'];
            $quantity       = intval($_POST['quantity']);
            $customer_id    = $_SESSION['customer_id'];

            $insert = "INSERT INTO cart (product_id, customer_id, quantity, color, size)
                    VALUES ('$product_id', '$customer_id', '$quantity', '$selected_color', '$selected_size')";
            mysqli_query($conn, $insert);
            echo "<script>alert('Added to cart successfully');</script>";
        }
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title> Velvet Vlogue </title>
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

<!-- Product Details Section -->
<form method="POST">
<div class="product-details-container">

    <div class="product-image">
        <img src="Admin/<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>">
    </div>

    <div class="product-info">
        <h2 class="product-name"><?php echo $product['product_name']; ?></h2>
        <p class="product-description"><?php echo $product['description']; ?></p>
        <p class="product-price">Rs <?php echo number_format($product['price'], 2); ?></p>

        <div class="product-options">
            <div class="option-group">
                <label>Size:</label>
                <div class="size-options">
                    <?php foreach($sizes as $s): ?>
                        <button type="button" class="size-btn"><?php echo $s; ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="size" id="selected-size" required>
            </div>

            <div class="option-group">
                <label>Color:</label>
                <div class="color-options">
                    <?php foreach($colors as $c): 
                        $c_trim = strtolower(trim($c));
                    ?>
                        <button type="button" class="color-btn" style="background-color: <?php echo $c_trim; ?>;"></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="color" id="selected-color" required>
            </div>

            <div class="option-group quantity">
                <label>Quantity:</label>
               <div class="quantity-selector">
                    <button type="button" class="decrement">-</button>
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="button" class="increment">+</button>
                </div>
            </div>
        </div>
        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
        
    </div>
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
    // SIZE SELECTION
    const sizeButtons = document.querySelectorAll('.size-btn');
    const selectedSizeInput = document.getElementById('selected-size');

    sizeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove 'selected' from all size buttons
            sizeButtons.forEach(b => b.classList.remove('selected'));
            // Add 'selected' to clicked button
            btn.classList.add('selected');
            // Set hidden input value
            selectedSizeInput.value = btn.textContent;
        });
    });

    // COLOR SELECTION
    const colorButtons = document.querySelectorAll('.color-btn');
    const selectedColorInput = document.getElementById('selected-color');

    colorButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove 'selected' from all color buttons
            colorButtons.forEach(b => b.classList.remove('selected'));
            // Add 'selected' to clicked button
            btn.classList.add('selected');
            // Set hidden input value
            selectedColorInput.value = btn.style.backgroundColor;
        });
    });

    // QUANTITY INCREMENT/DECREMENT
    const incrementBtn = document.querySelector('.increment');
    const decrementBtn = document.querySelector('.decrement');
    const quantityInput = document.querySelector('.quantity-selector input');

    incrementBtn.addEventListener('click', () => {
        quantityInput.value = parseInt(quantityInput.value) + 1;
    });

    decrementBtn.addEventListener('click', () => {
        if(quantityInput.value > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

    const form = document.querySelector('form');
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    form.addEventListener('submit', (e) => {
        if(selectedSizeInput.value === '' || selectedColorInput.value === '') {
            e.preventDefault(); // prevent form submission
            alert('Please select a size and a color before adding to cart!');
        }
    });
</script>
</body>
</html>