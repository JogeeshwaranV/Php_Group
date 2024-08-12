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

$cartItemID = intval($_POST["cartItemID"]);
$quantity = intval($_POST["quantity"]);

$sql = "UPDATE CartItems SET quantity = ? WHERE cartItemID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $quantity, $cartItemID);
$stmt->execute();

$conn->close();

header("Location: cart.php");
exit();
