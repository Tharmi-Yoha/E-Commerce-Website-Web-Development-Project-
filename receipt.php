<?php
session_start();
$conn = new mysqli("localhost","root","","wdd_velvet_vogue_db");
$order_id = intval($_GET['order_id']);

$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id"));
$items = mysqli_query($conn, "SELECT oi.*, p.product_name, p.image 
FROM order_items oi
JOIN products p ON oi.product_id=p.product_id
WHERE order_id=$order_id");
?>

<h2>Order Receipt</h2>
<p>Order ID: <?php echo $order['order_id']; ?></p>
<p>Date: <?php echo $order['order_date']; ?></p>
<p>Status: <?php echo $order['status']; ?></p>

<table>
    <thead>
        <tr>
            <th>Product</th><th>Name</th><th>Size</th><th>Color</th><th>Quantity</th><th>Price</th><th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while($i = mysqli_fetch_assoc($items)):
            $line_total = $i['quantity'] * $i['price'];
        ?>
        <tr>
            <td><img src="Admin/<?php echo $i['image']; ?>" width="50"></td>
            <td><?php echo $i['product_name']; ?></td>
            <td><?php echo $i['size']; ?></td>
            <td><?php echo $i['color']; ?></td>
            <td><?php echo $i['quantity']; ?></td>
            <td>Rs <?php echo number_format($i['price'],2); ?></td>
            <td>Rs <?php echo number_format($line_total,2); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<p><strong>Total Paid:</strong> Rs <?php echo number_format($order['total_amount'],2); ?></p>

<a href="HOME PAGE.php"><button>Back to Home</button></a>