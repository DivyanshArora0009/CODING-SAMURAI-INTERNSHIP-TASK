<?php
// website.php
session_start();

// Initialize CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize products with placeholder images
$products = [
    1 => [
        'id' => 1, 
        'name' => 'Wireless Headphones', 
        'description' => 'Premium noise-cancelling headphones with 30hr battery', 
        'price' => 129.99, 
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=300', 
        'category' => 'audio'
    ],
    2 => [
        'id' => 2, 
        'name' => 'Smart Watch', 
        'description' => 'Fitness tracker with heart rate monitor and GPS', 
        'price' => 199.99, 
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=300', 
        'category' => 'wearables'
    ],
    3 => [
        'id' => 3, 
        'name' => 'Bluetooth Speaker', 
        'description' => 'Portable waterproof speaker with deep bass', 
        'price' => 79.99, 
        'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=300', 
        'category' => 'audio'
    ],
    4 => [
        'id' => 4, 
        'name' => 'Gaming Mouse', 
        'description' => 'High-precision gaming mouse with RGB lighting', 
        'price' => 59.99, 
        'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=300', 
        'category' => 'computers'
    ],
    5 => [
        'id' => 5, 
        'name' => 'Mechanical Keyboard', 
        'description' => 'RGB backlit mechanical keyboard with blue switches', 
        'price' => 89.99, 
        'image' => 'https://images.unsplash.com/photo-1601445638532-3c6f6c3aa1d6?auto=format&fit=crop&w=300', 
        'category' => 'computers'
    ],
    6 => [
        'id' => 6, 
        'name' => 'External SSD', 
        'description' => '1TB portable SSD with USB-C connectivity', 
        'price' => 149.99, 
        'image' => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&fit=crop&w=300', 
        'category' => 'storage'
    ],
    7 => [
        'id' => 7, 
        'name' => '4K Monitor', 
        'description' => '27-inch 4K UHD display with HDR support', 
        'price' => 299.99, 
        'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTF29TcrNgQrOLGpuZ27Mr6bzWgbOWhBAJLSA&s', 
        'category' => 'computers'
    ],
    8 => [
        'id' => 8, 
        'name' => 'Wireless Earbuds', 
        'description' => 'True wireless earbuds with charging case', 
        'price' => 89.99, 
        'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=300', 
        'category' => 'audio'
    ],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Security token mismatch!";
        header("Location: website.php");
        exit;
    }

    // User registration
    if (isset($_POST['register'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format!";
        } else {
            // Simulate user creation
            $_SESSION['user'] = [
                'id' => uniqid(),
                'name' => $name,
                'email' => $email,
                'created_at' => time()
            ];
            $_SESSION['message'] = "Registration successful! You are now logged in.";
            header("Location: website.php");
            exit;
        }
    }
    
    // User login
    if (isset($_POST['login'])) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Simple demo authentication
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format!";
        } else {
            $_SESSION['user'] = [
                'id' => uniqid(),
                'name' => "Demo User",
                'email' => $email,
                'created_at' => time()
            ];
            $_SESSION['message'] = "Login successful!";
            header("Location: website.php");
            exit;
        }
    }
    
    // Add to cart
    if (isset($_POST['add_to_cart'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
        
        if (isset($products[$product_id])) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $_SESSION['message'] = "Product added to cart!";
        }
        header("Location: website.php");
        exit;
    }
    
    // Update cart quantities
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = max(0, (int)$quantity);
            
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        $_SESSION['message'] = "Cart updated!";
        header("Location: website.php?page=cart");
        exit;
    }
    
    // Checkout process
    if (isset($_POST['checkout'])) {
        // Validate form data
        $required = ['name', 'email', 'address', 'city', 'zip', 'card_name', 'card_number', 'exp_date', 'cvv'];
        $valid = true;
        
        foreach ($required as $field) {
            if (empty(trim($_POST[$field]))) {
                $valid = false;
                break;
            }
        }
        
        // Payment validation
        $card_number = preg_replace('/\D/', '', $_POST['card_number']);
        $exp_date = $_POST['exp_date'];
        $cvv = $_POST['cvv'];
        
        if (!$valid) {
            $_SESSION['error'] = "Please fill in all required fields!";
        } elseif (strlen($card_number) < 15 || strlen($card_number) > 16) {
            $_SESSION['error'] = "Invalid card number!";
        } elseif (!preg_match('/^\d{2}\/\d{2}$/', $exp_date)) {
            $_SESSION['error'] = "Invalid expiration date format (MM/YY)!";
        } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
            $_SESSION['error'] = "Invalid CVV!";
        } else {
            // Simulate payment processing
            $payment_success = true; // Always successful for demo
            
            if ($payment_success) {
                // Create order
                $order_id = 'ORD-' . strtoupper(uniqid());
                $_SESSION['order'] = [
                    'id' => $order_id,
                    'items' => $_SESSION['cart'],
                    'total' => array_sum(array_map(function($id, $qty) use ($products) {
                        return $products[$id]['price'] * $qty;
                    }, array_keys($_SESSION['cart']), array_values($_SESSION['cart']))),
                    'shipping' => [
                        'name' => htmlspecialchars($_POST['name']),
                        'email' => htmlspecialchars($_POST['email']),
                        'address' => htmlspecialchars($_POST['address']),
                        'city' => htmlspecialchars($_POST['city']),
                        'zip' => htmlspecialchars($_POST['zip'])
                    ],
                    'timestamp' => time()
                ];
                
                // Clear cart
                $_SESSION['cart'] = [];
                
                header("Location: website.php?page=thankyou");
                exit;
            } else {
                $_SESSION['error'] = "Payment failed. Please try again.";
            }
        }
        header("Location: website.php?page=checkout");
        exit;
    }
}

// Handle GET actions
if (isset($_GET['action'])) {
    // Remove from cart
    if ($_GET['action'] === 'remove' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['message'] = "Product removed from cart!";
        }
        header("Location: website.php?page=cart");
        exit;
    }
    
    // Logout
    if ($_GET['action'] === 'logout') {
        unset($_SESSION['user']);
        $_SESSION['message'] = "You have been logged out.";
        header("Location: website.php");
        exit;
    }
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Calculate cart total
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products[$id])) {
        $cart_total += $products[$id]['price'] * $qty;
        $cart_count += $qty;
    }
}

// Calculate cart summary
$cart_items = [];
foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products[$id])) {
        $cart_items[] = [
            'product' => $products[$id],
            'quantity' => $qty,
            'subtotal' => $products[$id]['price'] * $qty
        ];
    }
}

// Define all page content
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Your Electronics Store</title>
    <style>
        /* CSS Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --accent-dark: #c0392b;
            --light: #ecf0f1;
            --dark: #34495e;
            --success: #2ecc71;
            --warning: #f39c12;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        ul {
            list-style: none;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            text-align: center;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-accent {
            background-color: var(--accent);
        }
        
        .btn-accent:hover {
            background-color: var(--accent-dark);
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        /* Header Styles */
        header {
            background-color: var(--secondary);
            color: white;
            padding: 1rem 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .logo span {
            color: var(--primary);
        }
        
        .logo i {
            margin-right: 0.5rem;
        }
        
        nav ul {
            display: flex;
            gap: 1rem;
        }
        
        nav a {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .cart-icon {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .cart-icon:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .cart-count {
            background-color: var(--accent);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Main Content */
        main {
            padding: 2rem 0;
            flex: 1;
        }
        
        .page-title {
            margin-bottom: 2rem;
            color: var(--secondary);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary);
        }
        
        .section-title {
            margin: 2rem 0 1.5rem;
            color: var(--secondary);
            font-size: 1.5rem;
        }
        
        /* Messages */
        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
        }
        
        .message-success {
            background-color: rgba(46, 204, 113, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .message-error {
            background-color: rgba(231, 76, 60, 0.2);
            border: 1px solid var(--accent);
            color: var(--accent);
        }
        
        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            height: 200px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            color: var(--accent);
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .product-description {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .product-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Cart Page */
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .cart-items {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }
        
        .cart-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 120px;
            height: 120px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            flex-shrink: 0;
        }
        
        .cart-item-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .cart-item-price {
            color: var(--accent);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .cart-item-quantity input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        
        .remove-item {
            color: var(--accent);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .cart-summary {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            height: fit-content;
        }
        
        .summary-title {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 1.2rem;
            margin: 1.5rem 0;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        /* Forms */
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            border-radius: var(--border-radius);
            margin-bottom: 3rem;
        }
        
        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        /* Category Navigation */
        .category-nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .category-btn {
            padding: 0.6rem 1.2rem;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 30px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .category-btn:hover, .category-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: auto;
        }
        
        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: var(--primary);
            padding-left: 5px;
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .cart-container {
                grid-template-columns: 1fr;
            }
            
            .cart-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .cart-item-details {
                width: 100%;
            }
            
            .hero {
                height: 300px;
            }
            
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-container">
            <a href="?page=home" class="logo">
                <i>🛒</i> Tech<span>Shop</span>
            </a>
            
            <nav>
                <ul>
                    <li><a href="?page=home">Home</a></li>
                    <li><a href="?page=products">Products</a></li>
                    <li><a href="?page=about">About</a></li>
                    <li><a href="?page=contact">Contact</a></li>
                </ul>
            </nav>
            
            <div class="user-actions">
                <a href="?page=cart" class="cart-icon">
                    <i>🛒</i>
                    <div class="cart-count"><?= $cart_count ?></div>
                </a>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?></div>
                    <a href="?action=logout" class="btn btn-outline btn-small">Logout</a>
                <?php else: ?>
                    <a href="?page=login" class="btn btn-outline btn-small">Login</a>
                    <a href="?page=register" class="btn btn-small">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main class="container">
        <!-- Messages display -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message message-success">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message message-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Page content -->
        <!-- Home page -->
        <div id="home-content" class="page-content" style="display: <?= $page === 'home' ? 'block' : 'none' ?>;">
            <div class="hero">
                <div class="hero-content">
                    <h2>Discover Amazing Gadgets</h2>
                    <p>Find the latest tech products at unbeatable prices with free shipping on all orders</p>
                    <a href="?page=products" class="btn">Shop Now</a>
                </div>
            </div>

            <h2 class="section-title">Featured Products</h2>
            <div class="products-grid">
                <?php
                $featured = array_slice($products, 0, 4);
                foreach ($featured as $product): ?>
                    <div class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
                        <div class="product-image">
                            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                            <form method="post" class="product-actions">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="number" name="quantity" value="1" min="1" style="width: 60px; padding: 0.5rem;">
                                <button type="submit" name="add_to_cart" class="btn btn-accent">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="?page=products" class="btn btn-outline">View All Products</a>
            </div>
        </div>

        <!-- Products page -->
        <div id="products-content" class="page-content" style="display: <?= $page === 'products' ? 'block' : 'none' ?>;">
            <h1 class="page-title">All Products</h1>

            <div class="category-nav">
                <button class="category-btn active" data-category="all">All Products</button>
                <button class="category-btn" data-category="audio">Audio</button>
                <button class="category-btn" data-category="wearables">Wearables</button>
                <button class="category-btn" data-category="computers">Computers</button>
                <button class="category-btn" data-category="storage">Storage</button>
            </div>

            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
                        <div class="product-image">
                            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                            <div class="product-actions">
                                <a href="?page=product&id=<?= $product['id'] ?>" class="btn btn-outline" style="flex: 1;">View Details</a>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-accent">Add</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product detail page -->
        <div id="product-content" class="page-content" style="display: <?= $page === 'product' ? 'block' : 'none' ?>;">
            <?php
            if (isset($_GET['id'])) {
                $product_id = (int)$_GET['id'];
                if (isset($products[$product_id])) {
                    $product = $products[$product_id];
                    ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div class="product-image" style="height: 400px; background-color: #f5f5f5; border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center;">
                            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        
                        <div>
                            <h1 class="page-title"><?= htmlspecialchars($product['name']) ?></h1>
                            <div style="font-size: 1.8rem; color: var(--accent); font-weight: bold; margin-bottom: 1rem;">$<?= number_format($product['price'], 2) ?></div>
                            <p style="margin-bottom: 2rem;"><?= htmlspecialchars($product['description']) ?></p>
                            
                            <form method="post" style="max-width: 400px;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control">
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-block">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "<div class='message message-error'>Product not found!</div>";
                }
            } else {
                echo "<div class='message message-error'>Product ID not specified!</div>";
            }
            ?>
        </div>

        <!-- Shopping cart page -->
        <div id="cart-content" class="page-content" style="display: <?= $page === 'cart' ? 'block' : 'none' ?>;">
            <h1 class="page-title">Your Shopping Cart</h1>

            <?php if (empty($cart_items)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h2 style="margin-bottom: 1rem;">Your cart is empty</h2>
                    <p style="margin-bottom: 2rem;">Browse our products and add some items to your cart</p>
                    <a href="?page=products" class="btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <?php foreach ($cart_items as $item): 
                                $product = $item['product'];
                            ?>
                                <div class="cart-item">
                                    <div class="cart-item-image">
                                        <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    </div>
                                    <div class="cart-item-details">
                                        <h3 class="cart-item-name"><?= htmlspecialchars($product['name']) ?></h3>
                                        <div class="cart-item-price">$<?= number_format($product['price'], 2) ?></div>
                                        <div class="cart-item-quantity">
                                            <label for="quantity-<?= $product['id'] ?>">Quantity:</label>
                                            <input type="number" id="quantity-<?= $product['id'] ?>" name="quantities[<?= $product['id'] ?>]" value="<?= $item['quantity'] ?>" min="1">
                                            <a href="?action=remove&id=<?= $product['id'] ?>" class="remove-item">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                                <a href="?page=products" class="btn btn-outline">Continue Shopping</a>
                                <button type="submit" name="update_cart" class="btn">Update Cart</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="cart-summary">
                        <h3 class="summary-title">Order Summary</h3>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-row">
                                <span><?= htmlspecialchars($item['product']['name']) ?> x <?= $item['quantity'] ?></span>
                                <span>$<?= number_format($item['subtotal'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>$0.00</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Tax (8%)</span>
                            <span>$<?= number_format($cart_total * 0.08, 2) ?></span>
                        </div>
                        
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span>$<?= number_format($cart_total * 1.08, 2) ?></span>
                        </div>
                        
                        <a href="?page=checkout" class="btn btn-block">Proceed to Checkout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Checkout page -->
        <div id="checkout-content" class="page-content" style="display: <?= $page === 'checkout' ? 'block' : 'none' ?>;">
            <h1 class="page-title">Checkout</h1>

            <?php if (empty($cart_items)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h2 style="margin-bottom: 1rem;">Your cart is empty</h2>
                    <a href="?page=products" class="btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h2 style="margin-bottom: 1.5rem;">Shipping Information</h2>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Shipping Address *</label>
                                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="zip">ZIP Code *</label>
                                    <input type="text" id="zip" name="zip" class="form-control" required>
                                </div>
                            </div>
                            
                            <h2 style="margin: 2rem 0 1.5rem;">Payment Information</h2>
                            
                            <div class="form-group">
                                <label for="card_name">Name on Card *</label>
                                <input type="text" id="card_name" name="card_name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_number">Card Number *</label>
                                <input type="text" id="card_number" name="card_number" class="form-control" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="exp_date">Expiration Date (MM/YY) *</label>
                                    <input type="text" id="exp_date" name="exp_date" class="form-control" placeholder="MM/YY" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cvv">CVV *</label>
                                    <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" required>
                                </div>
                            </div>
                            
                            <button type="submit" name="checkout" class="btn btn-block" style="margin-top: 1.5rem;">Complete Purchase</button>
                        </form>
                    </div>
                    
                    <div>
                        <div class="cart-summary">
                            <h3 class="summary-title">Order Summary</h3>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="summary-row">
                                    <span><?= htmlspecialchars($item['product']['name']) ?> x <?= $item['quantity'] ?></span>
                                    <span>$<?= number_format($item['subtotal'], 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>$0.00</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Tax (8%)</span>
                                <span>$<?= number_format($cart_total * 0.08, 2) ?></span>
                            </div>
                            
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>$<?= number_format($cart_total * 1.08, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Login page -->
        <div id="login-content" class="page-content" style="display: <?= $page === 'login' ? 'block' : 'none' ?>;">
            <h1 class="page-title">Login</h1>

            <div class="form-container">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="form-group">
                        <label for="login_email">Email Address</label>
                        <input type="email" id="login_email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-block">Login</button>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <p>Don't have an account? <a href="?page=register">Register here</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Registration page -->
        <div id="register-content" class="page-content" style="display: <?= $page === 'register' ? 'block' : 'none' ?>;">
            <h1 class="page-title">Create Account</h1>

            <div class="form-container">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-block">Register</button>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <p>Already have an account? <a href="?page=login">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thank you page -->
        <div id="thankyou-content" class="page-content" style="display: <?= $page === 'thankyou' ? 'block' : 'none' ?>;">
            <?php if (isset($_SESSION['order'])): 
                $order = $_SESSION['order'];
                $order_total = $order['total'] * 1.08; // Including tax
                ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1.5rem;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    
                    <h1 class="page-title" style="text-align: center;">Thank You for Your Order!</h1>
                    
                    <div style="max-width: 600px; margin: 0 auto 2rem; background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Order Number:</span>
                            <span><strong><?= $order['id'] ?></strong></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Order Date:</span>
                            <span><?= date('F j, Y, g:i a', $order['timestamp']) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Total Amount:</span>
                            <span><strong>$<?= number_format($order_total, 2) ?></strong></span>
                        </div>
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                            <p style="text-align: left;">Your order will be shipped to:</p>
                            <address style="font-style: normal; margin-top: 0.5rem;">
                                <?= htmlspecialchars($order['shipping']['name']) ?><br>
                                <?= htmlspecialchars($order['shipping']['address']) ?><br>
                                <?= htmlspecialchars($order['shipping']['city']) ?>, <?= htmlspecialchars($order['shipping']['zip']) ?>
                            </address>
                        </div>
                    </div>
                    
                    <p style="font-size: 1.2rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                        You'll receive a confirmation email shortly with your order details.
                    </p>
                    
                    <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem;">
                        <a href="?page=home" class="btn">Continue Shopping</a>
                        <a href="?page=products" class="btn btn-outline">Browse More Products</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="message message-error">
                    No order found. Please complete your purchase first.
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="?page=products" class="btn">Browse Products</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- About page -->
        <div id="about-content" class="page-content" style="display: <?= $page === 'about' ? 'block' : 'none' ?>;">
            <h1 class="page-title">About TechShop</h1>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                <div>
                    <h2 style="margin-bottom: 1rem;">Our Story</h2>
                    <p style="margin-bottom: 1.5rem;">Founded in 2022, TechShop started as a small electronics store with a passion for bringing the latest technology to our customers. Over the years, we've grown into one of the leading online retailers for electronics and gadgets.</p>
                    <p>Our mission is to provide high-quality products at competitive prices with exceptional customer service. We carefully select all our products to ensure they meet our standards of quality and innovation.</p>
                </div>
                <div style="background-color: #f0f7ff; border-radius: var(--border-radius); padding: 2rem; display: flex; align-items: center; justify-content: center;">
                    <img src="https://images.unsplash.com/photo-1607082350899-7e105aa886ae?auto=format&fit=crop&w=600" alt="TechShop Store" style="width:100%; height:300px; border-radius: var(--border-radius); object-fit: cover;">
                </div>
            </div>

            <h2 class="section-title">Our Values</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div style="background-color: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; color: var(--primary);">Quality Products</h3>
                    <p>We source only the best electronics from trusted manufacturers to ensure you get reliable products that last.</p>
                </div>
                
                <div style="background-color: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; color: var(--primary);">Customer Satisfaction</h3>
                    <p>Your satisfaction is our top priority. Our support team is always ready to help with any questions or issues.</p>
                </div>
                
                <div style="background-color: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; color: var(--primary);">Fast Shipping</h3>
                    <p>We process orders quickly and ship most items within 24 hours so you get your products as soon as possible.</p>
                </div>
            </div>
        </div>

        <!-- Contact page -->
        <div id="contact-content" class="page-content" style="display: <?= $page === 'contact' ? 'block' : 'none' ?>;">
            <h1 class="page-title">Contact Us</h1>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
                <div>
                    <h2 style="margin-bottom: 1.5rem;">Get in Touch</h2>
                    <p style="margin-bottom: 1.5rem;">Have questions or feedback? We'd love to hear from you! Fill out the form and our team will get back to you as soon as possible.</p>
                    
                    <div style="margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 1rem;">Contact Information</h3>
                        <ul style="list-style: none;">
                            <li style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
                                <span>📍</span>
                                <span>Ghaziabad, Uttar Pradesh</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
                                <span>📞</span>
                                <span>+91-8860720565</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
                                <span>✉️</span>
                                <span>support@techshop.com</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 style="margin-bottom: 1rem;">Business Hours</h3>
                        <ul style="list-style: none;">
                            <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                <span>Monday - Friday</span>
                                <span>9:00 AM - 6:00 PM</span>
                            </li>
                            <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                <span>Saturday</span>
                                <span>10:00 AM - 4:00 PM</span>
                            </li>
                            <li style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span>Sunday</span>
                                <span>Closed</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div>
                    <div style="background-color: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                        <form>
                            <div class="form-group">
                                <label for="contact-name">Full Name</label>
                                <input type="text" id="contact-name" name="name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-email">Email Address</label>
                                <input type="email" id="contact-email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-subject">Subject</label>
                                <input type="text" id="contact-subject" name="subject" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-message">Message</label>
                                <textarea id="contact-message" name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-block">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>TechShop</h3>
                    <p>Your one-stop shop for all the latest electronics and gadgets at competitive prices.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="?page=home">Home</a></li>
                        <li><a href="?page=products">Products</a></li>
                        <li><a href="?page=about">About Us</a></li>
                        <li><a href="?page=contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Returns & Refunds</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li>Email: support@techshop.com</li>
                        <li>Phone: +91-8860720565</li>
                        <li>Address: Ghaziabad, Uttar Pradesh</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> TechShop. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Client-side JavaScript for interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Category filtering
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.dataset.category;
                    document.querySelectorAll('.product-card').forEach(card => {
                        if (category === 'all' || card.dataset.category === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Update active state
                    document.querySelectorAll('.category-btn').forEach(b => {
                        b.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
            
            // Form validation
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let valid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            valid = false;
                            field.style.borderColor = '#e74c3c';
                        } else {
                            field.style.borderColor = '';
                        }
                    });
                    
                    // Validate card number if present
                    const cardNumber = form.querySelector('#card_number');
                    if (cardNumber && cardNumber.value) {
                        const cleaned = cardNumber.value.replace(/\D/g, '');
                        if (cleaned.length < 15 || cleaned.length > 16) {
                            valid = false;
                            cardNumber.style.borderColor = '#e74c3c';
                        }
                    }
                    
                    // Validate expiration date if present
                    const expDate = form.querySelector('#exp_date');
                    if (expDate && expDate.value) {
                        if (!/^\d{2}\/\d{2}$/.test(expDate.value)) {
                            valid = false;
                            expDate.style.borderColor = '#e74c3c';
                        }
                    }
                    
                    // Validate CVV if present
                    const cvv = form.querySelector('#cvv');
                    if (cvv && cvv.value) {
                        if (!/^\d{3,4}$/.test(cvv.value)) {
                            valid = false;
                            cvv.style.borderColor = '#e74c3c';
                        }
                    }
                    
                    if (!valid) {
                        e.preventDefault();
                        alert('Please fill in all required fields correctly.');
                    }
                });
            });
        });
    </script>
</body>
</html>