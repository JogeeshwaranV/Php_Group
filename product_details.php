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

// Prepare and execute SQL query for product details
$sql = "SELECT g.*, d.Name AS developer_name FROM Games g JOIN Developers d ON g.DeveloperID = d.DeveloperID WHERE g.GameID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Prepare and execute SQL query for reviews
$sql_reviews = "SELECT r.*, u.Username FROM Reviews r JOIN Users u ON r.UserID = u.UserID WHERE r.GameID = ?";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $id);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();

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
</head>
<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .container-productdetails {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        background-color: black;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        text-align: center;
    }

    h2 {
        color: white !important;
    }

    img {
        max-width: 100%;
        height: auto;
        max-height: 500px;
        display: block;
        margin: 0 auto;
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

    .button-container-productdetails a,
    .button-container-productdetails button {
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

    .button-container-productdetails {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .add-to-cart-button {
        width: 150px;
        padding: 7px;
        border: none;
        background-color: #ffcc00 !important;
        color: #000 !important;
        font-size: 18px;
        font-weight: bolder;
        text-align: center;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .back-button {
        width: 190px;
        padding: 7px;
        border: none;
        font-size: 18px;
        font-weight: bolder;
        text-align: center;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .add-to-cart-button:hover {
        background-color: #ec9b00 !important;

    }

    .back-button {
        background-color: teal !important;
        color: black !important;
    }

    .back-button:hover {
        background-color: #10544c !important;
    }

    .reviews-section {
        margin-top: 30px;
    }

    .review {
        background-color: #333;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
        color: #fff;
    }

    .review-author {
        font-style: italic;
        margin-bottom: 5px;
    }

    .review-content {
        font-size: 16px;
    }
</style>

<body>
    <?php include 'header.php'; ?>

    <div class="container-productdetails">
        <div class="product-details">
            <h1 class="product-title"><?php echo htmlspecialchars($product['Title']); ?></h1>
            <p class="product-category">Developer: <?php echo htmlspecialchars($product['developer_name']); ?></p>
            <img src="images/<?php echo htmlspecialchars($product['ImageFile']); ?>" alt="<?php echo htmlspecialchars($product['Title']); ?>">
            <p class="product-price">$<?php echo htmlspecialchars($product['Price']); ?></p>
            <p class="product-description"><?php echo htmlspecialchars($product['Description']); ?></p>

            <form action="add_to_cart.php" method="POST">
                <div class="quantity">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1">
                </div>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['GameID']); ?>">

                <div class="button-container-productdetails">
                    <button type="submit" class="add-to-cart-button">Add to Cart</button>
                    <a href="products.php" class="back-button">Back to Products</a>
                </div>
            </form>
        </div>

        <div class="reviews-section">
            <h2>Reviews</h2>
            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                <div class="review">
                    <p class="review-content"><?php echo htmlspecialchars($review['Comment']); ?></p>
                    <p class="review-author">-<?php echo htmlspecialchars($review['Username']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>

<?php $conn->close(); ?>