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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION["user"];

// Get the user's cart
$sql = "
SELECT ci.cartItemID, g.Title, g.ImageFile, ci.quantity, ci.priceAtAddition, (ci.quantity * ci.priceAtAddition) AS total
FROM CartItems ci
JOIN ShoppingCarts sc ON ci.cartID = sc.cartID
JOIN Games g ON ci.gameID = g.GameID
WHERE sc.userID = ? AND sc.purchased = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;
$cart_empty = ($result->num_rows == 0); // Check if the cart is empty
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .container-cart {
            max-width: 80%;
            margin: 40px auto;
            padding: 2rem;
            background-color: black;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 1.5rem 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: teal;
            color: white;
        }

        td {
            background-color: #333;
        }

        .game-image {
            height: 240px;
            width: 210px;
            border-radius: 5px;
        }

        .total-row {
            font-weight: bold;
            background-color: #444;
        }

        .update-button,
        .remove-button {
            padding: 5px 10px;
            margin-top: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button {
            background-color: #ffcc00;
            color: black;
        }

        .remove-button {
            background-color: red;
            color: white;
        }

        .update-button:hover,
        .remove-button:hover {
            opacity: 0.8;
        }

        .empty-cart-message {
            text-align: center;
            font-size: 1.2em;
            padding: 20px;
            background-color: #444;
            color: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .quantity {
            width: 30%;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container-cart">
        <h1>Your Cart</h1>

        <?php if ($cart_empty): ?>
            <div class="empty-cart-message">Your cart is empty. <a href="index.php" style="color: #ffcc00; text-decoration: none;">Start shopping!</a></div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Title']); ?></td>
                            <td><img src="images/<?php echo htmlspecialchars($row['ImageFile']); ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>" class="game-image"></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td>$<?php echo number_format($row['priceAtAddition'], 2); ?></td>
                            <td>$<?php echo number_format($row['total'], 2); ?></td>
                            <td>
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cartItemID" value="<?php echo $row['cartItemID']; ?>">
                                    <input class="quantity" type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1">
                                    <button type="submit" class="update-button">Update</button>
                                </form>
                                <form action="remove_from_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cartItemID" value="<?php echo $row['cartItemID']; ?>">
                                    <button type="submit" class="remove-button">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php $total_amount += $row['total']; ?>
                    <?php endwhile; ?>
                    <tr class="total-row">
                        <td colspan="4">Total:</td>
                        <td>$<?php echo number_format($total_amount, 2); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <a href="checkout.php" class="update-button" style="text-decoration:none; text-align:center; display:block;">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php
$conn->close();
?>