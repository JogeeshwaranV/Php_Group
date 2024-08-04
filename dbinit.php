<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, $dbname);

// Drop tables if they exist
mysqli_query($conn, "DROP TABLE IF EXISTS users");
mysqli_query($conn, "DROP TABLE IF EXISTS categories");
mysqli_query($conn, "DROP TABLE IF EXISTS products");

// Create `users` table
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if (!mysqli_query($conn, $sql)) {
    die("Error creating users table: " . mysqli_error($conn));
}

// Create `categories` table
$sql = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
)";
if (!mysqli_query($conn, $sql)) {
    die("Error creating categories table: " . mysqli_error($conn));
}

// Create `products` table
$sql = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    short_description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 0,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)";
if (!mysqli_query($conn, $sql)) {
    die("Error creating products table: " . mysqli_error($conn));
}

// Insert sample data into `categories` table
$categories = [
    'Action',
    'Adventure',
    'RPG',
    'Sports',
    'Shooter'
];

foreach ($categories as $category) {
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
}

// Get category IDs
$categoryIds = [];
$sql = "SELECT id, name FROM categories";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $categoryIds[$row['name']] = $row['id'];
}

// Insert sample data into `products` table
$products = [
    ['name' => 'The Last of Us Part II', 'short_description' => 'Action-adventure game', 'long_description' => 'The Last of Us Part II is an action-adventure game played from a third-person perspective.', 'price' => 59.99, 'image' => 'last_of_us_part_ii.jpg', 'quantity' => 100, 'category' => 'Action'],
    ['name' => 'Ghost of Tsushima', 'short_description' => 'Open-world action-adventure game', 'long_description' => 'Ghost of Tsushima is an open-world action-adventure game set in feudal Japan.', 'price' => 49.99, 'image' => 'ghost_of_tsushima.jpg', 'quantity' => 200, 'category' => 'Action'],
    ['name' => 'Cyberpunk 2077', 'short_description' => 'Action RPG', 'long_description' => 'Cyberpunk 2077 is an action RPG set in a dystopian future.', 'price' => 69.99, 'image' => 'cyberpunk_2077.jpg', 'quantity' => 60, 'category' => 'RPG'],
    ['name' => 'God of War', 'short_description' => 'Action-adventure game', 'long_description' => 'God of War is an action-adventure game that follows the journey of Kratos in the Norse mythology.', 'price' => 39.99, 'image' => 'god_of_war.jpg', 'quantity' => 200, 'category' => 'Action'],
    ['name' => 'Spider-Man', 'short_description' => 'Action-adventure game', 'long_description' => 'Spider-Man is an action-adventure game that features the titular superhero from Marvel Comics.', 'price' => 49.99, 'image' => 'spider_man.jpg', 'quantity' => 200, 'category' => 'Action'],
    ['name' => 'Red Dead Redemption 2', 'short_description' => 'Action-adventure game', 'long_description' => 'Red Dead Redemption 2 is an action-adventure game set in the late 19th century America.', 'price' => 59.99, 'image' => 'red_dead_redemption_2.jpg', 'quantity' => 200, 'category' => 'Adventure'],
    ['name' => 'Horizon Zero Dawn', 'short_description' => 'Action RPG', 'long_description' => 'Horizon Zero Dawn is an action RPG set in a post-apocalyptic world where robotic creatures dominate.', 'price' => 29.99, 'image' => 'horizon_zero_dawn.jpg', 'quantity' => 200, 'category' => 'RPG'],
    ['name' => 'Death Stranding', 'short_description' => 'Action game', 'long_description' => 'Death Stranding is an action game set in a post-apocalyptic world with an emphasis on exploration.', 'price' => 59.99, 'image' => 'death_stranding.jpg', 'quantity' => 200, 'category' => 'Action'],
    ['name' => 'Uncharted 4', 'short_description' => 'Action-adventure game', 'long_description' => 'Uncharted 4 is an action-adventure game that continues the story of Nathan Drake.', 'price' => 39.99, 'image' => 'uncharted_4.jpg', 'quantity' => 200, 'category' => 'Action'],
    ['name' => 'Bloodborne', 'short_description' => 'Action RPG', 'long_description' => 'Bloodborne is an action RPG set in a dark and gothic world with a focus on fast-paced combat.', 'price' => 29.99, 'image' => 'bloodborne.jpg', 'quantity' => 80, 'category' => 'RPG'],
    ['name' => 'FIFA 21', 'short_description' => 'Football simulation game', 'long_description' => 'FIFA 21 is a football simulation game that offers realistic gameplay and various modes to play.', 'price' => 59.99, 'image' => 'fifa_21.jpg', 'quantity' => 200, 'category' => 'Sports'],
    ['name' => 'NBA 2K21', 'short_description' => 'Basketball simulation game', 'long_description' => 'NBA 2K21 is a basketball simulation game that provides an immersive and realistic basketball experience.', 'price' => 59.99, 'image' => 'nba_2k21.jpg', 'quantity' => 200, 'category' => 'Sports']
];

foreach ($products as $product) {
    $categoryId = $categoryIds[$product['category']];
    $sql = "INSERT INTO products (name, short_description, long_description, price, image, category_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssssi", $product['name'], $product['short_description'], $product['long_description'], $product['price'], $product['image'], $categoryId);
    mysqli_stmt_execute($stmt);
}

echo "Database and tables initialized successfully";

mysqli_close($conn);
?>
