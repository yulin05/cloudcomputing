<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';

// Fetch all products
$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
$products = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Shop - Your Premier Graduation Destination</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc; /* Beige background */
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
        
        .about-section {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .about-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('images/graduation-bg.jpg');
            background-size: cover;
            opacity: 0.1;
            z-index: -1;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .mission {
            font-weight: bold;
            margin: 20px 0;
        }
        
        .thanks {
            margin-top: 30px;
            font-style: italic;
        }
        
        .featured-products {
            margin-top: 40px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #45a049;
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
        <div class="about-section">
            <h1>About Us</h1>
            <p>-- Welcome to Golden Shop, your premier destination for all your graduation needs! --</p>
            
            <p>We specialize in providing top-quality graduation products, from caps and gowns to personalized keepsakes and gifts.</p>
            
            <div class="mission">
                <h2>Our Mission</h2>
                <p>Our mission is to make your graduation experience seamless and memorable, offering a hassle-free shopping experience with fast shipping and dedicated customer service. Whether you're a proud graduate, supportive family member, or friend, we have everything you need to celebrate this momentous occasion in style.</p>
            </div>
            
            <div class="thanks">
                <p>-- Thank you for choosing Golden Shop --</p>
                <p>here's to commemorating your achievements and embracing the exciting journey ahead!</p>
            </div>
        </div>
        
        <div class="featured-products">
            <h2>Featured Products</h2>
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
        </div>
    </div>
    
    <footer>
        <p>© 2025 Golden Shop - Your Graduation Destination</p>
        <p>Tel: +6012-3456789 | Email: info@goldenshop.com</p>
    </footer>
</body>
</html>