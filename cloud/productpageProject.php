<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug logging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data: " . print_r($_POST, true));
}

include 'db_connection.php';

// Fetch all products if no specific product is selected
$search_sql = ''; 

// Check if search query is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);  // Sanitize user input
    $search_sql = " AND product_name LIKE '%$search%'";  // Append search condition to SQL
}

// Fetch all products if no specific product is selected, or a search term is provided
if (!isset($_GET['id'])) {
    $sql = "SELECT * FROM product WHERE 1=1" . $search_sql;  // Add search condition to query
    $result = mysqli_query($conn, $sql);
    $products = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
} else {
    // Fetch specific product
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM product WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
}

// Handle add to cart action
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Get product details with prepared statement
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    $total_price = $product['price'] * $quantity;
    
    // Add to orders table with prepared statement
    $insert_stmt = $conn->prepare("INSERT INTO orders (product_id, product_name, price, quantity, total_price, photo_url) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("isdiis", $product_id, $product['product_name'], $product['price'], $quantity, $total_price, $product['photo_url']);
    
    if ($insert_stmt->execute()) {
        $message = "Product added to cart successfully!";
    } else {
        $message = "Error adding product to cart: " . mysqli_error($conn);
    }
    
    $insert_stmt->close();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Shop - Products</title>
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

        .search-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
        }

        .search-form {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .search-form input {
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
            width: 250px;
            transition: border-color 0.3s;
        }

        .search-form input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .search-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #45a049;
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
        }
        
        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .product-name {
            font-weight: bold;
            margin: 10px 0;
        }
        
        .product-price {
            color: #e67e22;
            font-weight: bold;
            font-size: 30px;
            margin-bottom: 10px;
        }
        
        .product-description {
            color: #666;
            margin-bottom: 15px;
            text-align: left;
        }
        
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .back-btn {
            background-color: #2196F3;
            margin-top: 150px;
        }
        
        .back-btn:hover {
            background-color: #0b7dda;
        }
        
        .single-product {
            display: flex;
            gap: 30px;
        }
        
        .product-image {
            flex: 1;
        }
        
        .product-details {
            flex: 2;
        }
        
        .product-image img {
            max-width: 100%;
            border-radius: 8px;
        }
        
        .quantity-input {
            padding: 8px;
            width: 60px;
            margin-right: 10px;
        }
        
        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        .modal-btn {
            padding: 8px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .ok-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .cancel-btn {
            background-color: #f44336;
            color: white;
        }
        
        .success-message {
            display: none;
            position: fixed;
            top: 100px;
            right: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 2;
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

        <div class="search-container">
            <form method="get" action="" class="search-form">
                <input type="text" name="search" placeholder="Search Product by Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['id']) && isset($product)): ?>
            <!-- Single Product View -->
            <div class="single-product">
                <div class="product-image">
                <img src="<?php echo BASE_URL . $product['photo_url']; ?>" alt="<?php echo $product['product_name']; ?>">
                </div>
                <div class="product-details">
                    <h1><?php echo $product['product_name']; ?></h1>
                    <div class="product-price"><strong>RM <?php echo number_format($product['price'], 2); ?></strong></div>
                    <div class="product-description">
                        <p><?php echo $product['description']; ?></p>
                    </div>
                    <form method="post" id="addToCartForm">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="add_to_cart" value="1">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="quantity-input" value="1" min="1" required>
                        <button type="button" class="btn" onclick="confirmAddToCart()">Add to Cart</button>
                    </form>
                    <a href="productpageProject.php" class="btn back-btn">Back to Products</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Products List View -->
            <h1>Our Products</h1>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                <img src="<?php echo BASE_URL . $product['photo_url']; ?>" alt="<?php echo $product['product_name']; ?>">
                    <div class="product-name"><?php echo $product['product_name']; ?></div>
                    <div class="product-price">RM <?php echo number_format($product['price'], 2); ?></div>
                    <a href="productpageProject.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to add this product to the cart?</p>
            <div class="modal-buttons">
                <button class="modal-btn ok-btn" onclick="submitForm()">OK</button>
                <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
    
    <!-- Success Message -->
    <div id="successMessage" class="success-message">
        <p>Product added to cart successfully!</p>
        <div>
            <a href="productpageProject.php" class="btn">Back to Products</a>
            <a href="cartProject.php" class="btn">View Cart</a>
        </div>
    </div>
    
    <footer>
        <p>© 2025 Graduation Shop - Your Graduation Destination</p>
        <p>Tel: +6012-3456789 | Email: info@graduationshop.com</p>
    </footer>
    
    <script>
        function confirmAddToCart() {
            document.getElementById('confirmModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
        
        function submitForm() {
            document.getElementById('addToCartForm').submit();
        }
        
        <?php if (!empty($message)): ?>
        // Show success message and buttons
        document.getElementById('successMessage').style.display = 'block';
        
        // Hide after 5 seconds
        setTimeout(function() {
            document.getElementById('successMessage').style.display = 'none';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>