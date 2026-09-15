<?php
    session_start();
    $host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "wdd_velvet_vogue_db";

    $conn = new mysqli($host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['add_to_cart'])){

        if(!isset($_SESSION['customer_id'])){
            echo "<script>alert('Please login first');</script>";
        } else {

            $product_id = $_POST['product_id'];
            $customer_id = $_SESSION['customer_id'];

            // Get product default size & color
            $product_query = "SELECT * FROM products WHERE product_id='$product_id'";
            $product_result = mysqli_query($conn, $product_query);
            $product = mysqli_fetch_assoc($product_result);

            $color = $product['color'];
            $size = $product['size'];

            $insert = "INSERT INTO cart (product_id, customer_id, quantity, color, size)
                    VALUES ('$product_id', '$customer_id', 1, '$color', '$size')";

            mysqli_query($conn, $insert);

            echo "<script>alert('Added to cart successfully');</script>";
        }
    }

    $where = "WHERE stock > 0"; // default to show only in-stock items

    // Category filter
    if(isset($_GET['category']) && count($_GET['category']) > 0){
        $categories = array_map('intval', $_GET['category']);
        $where .= " AND category_id IN (".implode(',', $categories).")";
    }

    // Gender filter
    if(isset($_GET['gender']) && $_GET['gender'] != ""){
        $gender = mysqli_real_escape_string($conn, $_GET['gender']);
        $where .= " AND gender='$gender'";
    }

    // Color filter
    if(isset($_GET['color']) && count($_GET['color']) > 0){
        $color_conditions = [];
        foreach($_GET['color'] as $c){
            $c = mysqli_real_escape_string($conn, $c);
            $color_conditions[] = "FIND_IN_SET('$c', color) > 0";
        }
        $where .= " AND (" . implode(' OR ', $color_conditions) . ")";
    }

    // Size filter
    if(isset($_GET['size']) && count($_GET['size']) > 0){
        $size_conditions = [];
        foreach($_GET['size'] as $s){
            $s = mysqli_real_escape_string($conn, $s);
            $size_conditions[] = "FIND_IN_SET('$s', size) > 0";
        }
        $where .= " AND (" . implode(' OR ', $size_conditions) . ")";
    }

    // Price filter
    if(isset($_GET['min_price']) && $_GET['min_price'] != ""){
        $min = floatval($_GET['min_price']);
        $where .= " AND price >= '$min'";
    }
    if(isset($_GET['max_price']) && $_GET['max_price'] != ""){
        $max = floatval($_GET['max_price']);
        $where .= " AND price <= '$max'";
    }

    // Search filter
    if(isset($_GET['search']) && $_GET['search'] != ""){
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $where .= " AND product_name LIKE '%$search%'";
    }
    
    // Fetch products
    $query = "SELECT * FROM products $where ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products | Velvet Vogue</title>
    <link rel="stylesheet" href="STYLEE.CSS">
    <style>
.new-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 per row */
    gap: 20px;
    padding: 10px;
}

</style>
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
<div class="categories-page " style="flot:left; ">
    <!-- Sidebar Filters -->
    <aside class="sidebar-filters" >
        <form method="GET" id="filter-form">
            <h3>Categories</h3>
            <div class="filter-group">
                <label><input type="checkbox" name="category[]" value="1" <?php if(isset($_GET['category']) && in_array(1, $_GET['category'])) echo "checked"; ?>> Mens Wear</label>
                <label><input type="checkbox" name="category[]" value="2" <?php if(isset($_GET['category']) && in_array(2, $_GET['category'])) echo "checked"; ?>> Womens Wear</label>
                <label><input type="checkbox" name="category[]" value="3" <?php if(isset($_GET['category']) && in_array(3, $_GET['category'])) echo "checked"; ?>> Kids Wear</label>
                <label><input type="checkbox" name="category[]" value="4" <?php if(isset($_GET['category']) && in_array(4, $_GET['category'])) echo "checked"; ?>> Accessories</label>
                <label><input type="checkbox" name="category[]" value="5" <?php if(isset($_GET['category']) && in_array(5, $_GET['category'])) echo "checked"; ?>> Footwear</label>
            </div>

            <h3>Gender</h3>
            <div class="filter-group">
                <label><input type="radio" name="gender" value="Male" <?php if(isset($_GET['gender']) && $_GET['gender']=='Male') echo "checked"; ?>> Male</label>
                <label><input type="radio" name="gender" value="Female" <?php if(isset($_GET['gender']) && $_GET['gender']=='Female') echo "checked"; ?>> Female</label>
            </div>

            <h3>Color</h3>
            <div class="filter-group">
                <?php 
                $colors = ['Green','Red','Yellow','Orange','Purple','Pink','Brown','Black','White','Grey','Violet','Indigo','Navy Blue','Gold'];
                foreach($colors as $color): ?>
                    <label>
                        <input type="checkbox" name="color[]" value="<?php echo $color; ?>" 
                        <?php if(isset($_GET['color']) && in_array($color, $_GET['color'])) echo "checked"; ?>> <?php echo $color; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <h3>Size</h3>
             <div class="filter-group">
                <?php $sizes = ['XS','S','M','L','XL'];
                foreach($sizes as $size_val): ?>
                    <label>
                        <input type="checkbox" name="size[]" value="<?php echo $size_val; ?>" 
                        <?php if(isset($_GET['size']) && in_array($size_val, $_GET['size'])) echo "checked"; ?>> <?php echo $size_val; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <h3>Price</h3>
            <div class="filter-group">
                <label>Min <input type="number" name="min_price" placeholder="0" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>"></label>
                <label>Max <input type="number" name="max_price" placeholder="0" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>"></label>
            </div>
            <button type="submit" class="apply-btn">Apply Filters</button>
            <a href="CATEGORIES PAGE.php" class="reset-btn">Reset Filters</a>
        </form>
    </aside>
    <div class="new-grid" style=";">
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="new-card">
            <img src="Admin/<?php echo $row['image']; ?>" alt="">
            <h3><?php echo $row['product_name']; ?></h3>
            <p class="price">Rs <?php echo number_format($row['price'],2); ?></p>

            <a href="PRODUCT-DETAILS.php?id=<?php echo $row['product_id']; ?>">
                <button class="view">View</button>
            </a>
        </div>
    <?php } ?>
    </div>
</div>


<!-- Footer -->
<footer class="footer" style="margin-top:auto;">
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

</body>
</html>