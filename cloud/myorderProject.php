<?php
include 'db_connection.php';
// Add this line right after the include statement
date_default_timezone_set('Asia/Kuala_Lumpur');

// Fetch purchased orders
$sql = "SELECT * FROM purchased_orders ORDER BY purchase_date DESC";
$result = mysqli_query($conn, $sql);
$purchases = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $purchases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Shop - Order History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc;
        }
        
        header {
            background-color: #000;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            text-transform: uppercase;
        }
        
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .no-orders {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #666;
        }
        
        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Golden Shop</div>
        <nav>
            <a href="homepageProject.php">Home</a>
            <a href="productpageProject.php">Product</a>
            <a href="cartProject.php">Cart</a>
            <a href="myorderProject.php">Order List</a>
            <a href="mypurchasesProject.php">Purchased History</a>
        </nav>
    </header>
    
    <div class="container">
        <h1>Your Order History</h1>
        
        <?php if (count($purchases) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Price</th>
                        <th>Payment Method</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><img src="<?php echo $purchase['photo_url']; ?>" alt="<?php echo $purchase['photo_url']; ?>" class="product-image"></td>
                            <td><?php echo $purchase['product_name']; ?></td>
                            <td><?php echo $purchase['quantity']; ?></td>
                            <td>RM <?php echo number_format($purchase['price'], 2); ?></td>
                            <td>RM <?php echo number_format($purchase['total_price'], 2); ?></td>
                            <td><?php echo $purchase['payment_method']; ?></td>
                            <td><?php $date = new DateTime($purchase['purchase_date']);
                                echo $date->format('Y-m-d H:i:s'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">You haven't made any purchases yet.</div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>© 2025 Golden Shop - Your Graduation Destination</p>
        <p>Tel: +6012-3456789 | Email: info@Graduation.com</p>
    </footer>
</body>
</html>