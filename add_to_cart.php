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

// Check if product ID is set
if (isset($_POST["product_id"])) {
    $gameID = intval($_POST["product_id"]);
} else {
    die("Error: Product ID is missing.");
}

// Get user ID and game details
$userID = $_SESSION["user"];
$quantity = intval($_POST["quantity"]);

// Check if the user already has a shopping cart
$sql = "SELECT cartID FROM ShoppingCarts WHERE userID = ? AND purchased = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // If no cart exists, create one
    $sql_insert_cart = "INSERT INTO ShoppingCarts (userID) VALUES (?)";
    $stmt_insert_cart = $conn->prepare($sql_insert_cart);
    $stmt_insert_cart->bind_param("i", $userID);
    $stmt_insert_cart->execute();
    $cartID = $stmt_insert_cart->insert_id;
} else {
    // If cart exists, get the cart ID
    $stmt->bind_result($cartID);
    $stmt->fetch();
}

// Check if the item is already in the cart
$sql_check_item = "SELECT cartItemID, quantity FROM CartItems WHERE cartID = ? AND gameID = ?";
$stmt_check_item = $conn->prepare($sql_check_item);
$stmt_check_item->bind_param("ii", $cartID, $gameID);
$stmt_check_item->execute();
$stmt_check_item->store_result();

if ($stmt_check_item->num_rows > 0) {
    // If item is already in the cart, update the quantity
    $stmt_check_item->bind_result($cartItemID, $existing_quantity);
    $stmt_check_item->fetch();
    $new_quantity = $existing_quantity + $quantity;
    $sql_update_item = "UPDATE CartItems SET quantity = ? WHERE cartItemID = ?";
    $stmt_update_item = $conn->prepare($sql_update_item);
    $stmt_update_item->bind_param("ii", $new_quantity, $cartItemID);
    $stmt_update_item->execute();
} else {
    // If item is not in the cart, insert it
    $sql_insert_item = "INSERT INTO CartItems (cartID, gameID, quantity, priceAtAddition) VALUES (?, ?, ?, (SELECT Price FROM Games WHERE GameID = ?))";
    $stmt_insert_item = $conn->prepare($sql_insert_item);
    $stmt_insert_item->bind_param("iiii", $cartID, $gameID, $quantity, $gameID);
    $stmt_insert_item->execute();
}

$conn->close();

// Redirect back to the product details page or cart page
header("Location: cart.php");
exit();
