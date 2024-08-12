<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamestore";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$id = intval($_GET['id']);

// Prepare and execute SQL query
$sql = "SELECT g.*, d.Name AS developer_name FROM Games g JOIN Developers d ON g.DeveloperID = d.DeveloperID WHERE g.GameID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['Title']); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: black;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
        }

        img:hover {
            transform: scale(1.02);
        }

        .product-details {
            padding: 20px;
            text-align: left;
        }

        .product-title {
            font-size: 32px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 10px;
        }

        .product-category {
            font-size: 16px;
            color: #fff;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: teal;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .product-price {
            font-size: 24px;
            color: #fff;
            margin-bottom: 20px;
        }

        .button-container a {
            text-decoration: none !important;
        }

        .product-description,
        .long-description {
            font-size: 16px;
            color: #fff;
            line-height: 1.6;
        }

        .quantity {
            margin: 20px 0;
            display: flex;
            align-items: center;
        }

        .quantity label {
            margin-right: 10px;
            font-size: 16px;
            color: #fff;
        }

        .quantity input {
            width: 60px;
            padding: 8px;
            font-size: 16px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .add-to-cart-button,
        .back-button {
            width: 150px;
            padding: 15px;
            border: none;
            background-color: teal;
            color: #fff;
            font-size: 18px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .add-to-cart-button:hover {
            background-color: #10544c;
        }

        .back-button {
            background-color: #5a6268;
        }

        .back-button:hover {
            background-color: blue;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="product-details">
            <h1 class="product-title"><?php echo htmlspecialchars($product['Title']); ?></h1>
            <p class="product-category">Developer: <?php echo htmlspecialchars($product['developer_name']); ?></p>
            <img src="images/<?php echo htmlspecialchars($product['Title']); ?>.jpg" alt="<?php echo htmlspecialchars($product['Title']); ?>">
            <p class="product-price">$<?php echo htmlspecialchars($product['Price']); ?></p>
            <p class="product-description"><?php echo htmlspecialchars($product['Description']); ?></p>

            <form action="add_to_cart.php" method="POST">
                <div class="quantity">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1">
                </div>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['GameID']); ?>">

                <div class="button-container">
                    <button type="submit" class="add-to-cart-button">Add to Cart</button>
                    <a href="products.php" class="back-button">Back to Products</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>