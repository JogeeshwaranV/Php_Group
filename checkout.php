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

// Get the total amount for the cart
$sql = "
SELECT SUM(ci.quantity * ci.priceAtAddition) AS total_amount
FROM CartItems ci
JOIN ShoppingCarts sc ON ci.cartID = sc.cartID
WHERE sc.userID = ? AND sc.purchased = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_amount = $row['total_amount'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .container-checkout {
            width: 800px;
            margin: 40px auto;
            padding: 2rem;
            background-color: black;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;

        }

        .total-row {
            font-weight: bold;
            background-color: #444;
            padding: 15px;
            border-radius: 8px;
        }

        .payment-details {
            background-color: #444;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .payment-details label {
            color: white;
            display: block;
            margin-top: 10px;
        }

        .payment-details input[type="text"],
        .payment-details input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .payment-details input[type="submit"] {
            background-color: #ffcc00;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }

        .payment-details input[type="submit"]:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container-checkout">
        <h1>Checkout</h1>

        <div class="total-row">
            <span>Total Amount: $<?php echo number_format($total_amount, 2); ?></span>
        </div>

        <div class="payment-details">
            <form action="process_payment.php" method="POST">
                <label for="address">Billing Address:</label>
                <input type="text" id="address" name="address" required>

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode" required>

                <label for="phoneNumber">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>

                <h4 class="mt-5">Card Details</h4>
                <label for="cardNumber">Card Number:</label>
                <input type="text" id="cardNumber" name="cardNumber" required>

                <label for="expiryDate">Expiry Date:</label>
                <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required>

                <label for="cvv">CVV:</label>
                <input type="number" id="cvv" name="cvv" required>

                <input type="submit" value="Pay Now">
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>