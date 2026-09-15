<?php
session_start();
if(!isset($_SESSION['customer_id'])){
    header("Location: SINGN_IN.php"); // redirect if not logged in
    exit();
}

$customer_id = $_SESSION['customer_id'];

$conn = new mysqli("localhost","root","","wdd_velvet_vogue_db");
if($conn->connect_error) die("DB Error: ".$conn->connect_error);

// Fetch cart items with product info
$sql = "SELECT c.cart_id, c.quantity, c.color, c.size,
               p.product_name, p.price, p.image
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.customer_id = $customer_id";

$result = mysqli_query($conn, $sql);

// Calculate subtotal
$subtotal = 0;

// Remove item from cart
if(isset($_GET['remove'])){
    $cart_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id=$cart_id AND customer_id=$customer_id");
    header("Location: SHOPPING CART.php");
    exit();
}

// Update quantity
if(isset($_POST['update_quantity'])){
    foreach($_POST['quantity'] as $cart_id => $qty){
        $cart_id = intval($cart_id);
        $qty = intval($qty);
        if($qty < 1) $qty = 1;
        mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE cart_id=$cart_id AND customer_id=$customer_id");
    }
    header("Location: SHOPPING CART.php");
    exit();
}

// Proceed to checkout
if(isset($_POST['checkout'])){
    // Calculate total
    $subtotal = 0;
    $cart_items = mysqli_query($conn, "SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id=p.product_id WHERE c.customer_id=$customer_id");
    while($item = mysqli_fetch_assoc($cart_items)){
        $subtotal += $item['quantity'] * $item['price'];
    }

    $shipping = 200;
    $total_amount = $subtotal + $shipping;

    // Insert into orders
    mysqli_query($conn, "INSERT INTO orders (customer_id, order_date, status, total_amount)
                         VALUES ($customer_id, NOW(), 'Processed', $total_amount)");
    $order_id = mysqli_insert_id($conn);

    // Insert each cart item into order_items
    $cart_items = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id=$customer_id");
    while($item = mysqli_fetch_assoc($cart_items)){
        $product_price = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE product_id=".$item['product_id']))['price'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, size, color, price)
                             VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, '{$item['size']}', '{$item['color']}', $product_price)");
    }

    // Clear cart
    mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$customer_id");

    // Redirect to receipt
    header("Location: receipt.php?order_id=$order_id");
    exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Velvet Vogue - Shopping Cart</title>
    <link rel="stylesheet" href="STYLEE.CSS">
</head>
<body>

<!-- Header same as homepage -->
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

<!-- Shopping Cart Content -->
<div class="cart-container">
    <h2>Your Shopping Cart</h2>
    <div class="accent-line"></div> 
    <div class="cart-box">
    <table class="cart-table">
        <thead>
        <tr>
        <th>Product</th>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Remove</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $subtotal = 0;
        $result = mysqli_query($conn, "SELECT c.cart_id, c.quantity, c.color, c.size, p.product_name, p.price, p.image 
                                    FROM cart c 
                                    JOIN products p ON c.product_id=p.product_id 
                                    WHERE c.customer_id=$customer_id");
        while($row = mysqli_fetch_assoc($result)):
            $total = $row['quantity'] * $row['price'];
            $subtotal += $total;
        ?>
        <tr>
        <td><img src="Admin/<?php echo $row['image']; ?>" width="50"></td>
        <td><?php echo $row['product_name']; ?> <br><small>Size: <?php echo $row['size']; ?>, Color: <?php echo $row['color']; ?></small></td>
        <td>Rs <?php echo number_format($row['price'],2); ?></td>
        <td>
        <form method="POST">
        <input type="number" name="quantity[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">
        <button type="submit" name="update_quantity">Update</button>
        </form>
        </td>
        <td>Rs <?php echo number_format($total,2); ?></td>
        <td><a href="?remove=<?php echo $row['cart_id']; ?>" onclick="return confirm('Remove this item?');">X</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        </table>

        <?php
            $shipping = 200;
            $total_amount = $subtotal + $shipping;
            ?>
            <div class="cart-summary">
            <h3>Cart Summary</h3>
            <div class="cart-sub-m"><p>Subtotal:</p><p>Rs <?php echo number_format($subtotal,2); ?></p></div>
            <div class="cart-sub-m"><p>Shipping:</p><p>Rs <?php echo number_format($shipping,2); ?></p></div>
            <div class="cart-sub-m"><p><strong>Total:</strong></p><p><strong>Rs <?php echo number_format($total_amount,2); ?></strong></p></div>

            <form method="POST">
            <button type="submit" name="checkout" class="checkout-btn">Proceed to Checkout</button>
            </form>
            </div>
        </div>
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

</body>
</html>