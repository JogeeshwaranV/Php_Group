<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require('./libs/fpdf/fpdf.php'); // Include FPDF library

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

// Validate form data
$address = $_POST['address'] ?? '';
$postalCode = $_POST['postalCode'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$cardNumber = $_POST['cardNumber'] ?? '';
$expiryDate = $_POST['expiryDate'] ?? '';
$cvv = $_POST['cvv'] ?? '';

if (empty($address) || empty($postalCode) || empty($phoneNumber) || empty($cardNumber) || empty($expiryDate) || empty($cvv)) {
    die("All fields are required.");
}

// Simulate payment processing (here you would integrate with a real payment gateway)
$payment_successful = true; // This is just a simulation

if ($payment_successful) {
    // Get the cart items
    $sql = "
    SELECT ci.cartItemID, g.GameID, g.Title, g.ImageFile, ci.quantity, ci.priceAtAddition, (ci.quantity * ci.priceAtAddition) AS total
    FROM CartItems ci
    JOIN ShoppingCarts sc ON ci.cartID = sc.cartID
    JOIN Games g ON ci.gameID = g.GameID
    WHERE sc.userID = ? AND sc.purchased = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = [];
    $total_amount = 0;

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_amount += $row['total'];
    }

    // Insert order details into Orders table
    $orderDate = date('Y-m-d H:i:s');
    $status = 'Pending';
    $sql = "INSERT INTO Orders (UserID, OrderDate, TotalAmount, Status) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $userID, $orderDate, $total_amount, $status);
    $stmt->execute();
    $orderID = $stmt->insert_id; // Get the ID of the newly inserted order

    // Insert each cart item into OrderItems table
    $sql = "INSERT INTO OrderItems (OrderID, GameID, Quantity, PriceAtPurchase) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($cart_items as $item) {
        $stmt->bind_param("iiid", $orderID, $item['GameID'], $item['quantity'], $item['priceAtAddition']);
        $stmt->execute();
    }

    // Mark the cart as purchased
    $sql = "UPDATE ShoppingCarts SET purchased = 1 WHERE userID = ? AND purchased = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    // Generate PDF invoice
    $pdf = new FPDF();
    $pdf->AddPage();

    // Invoice Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

    // Billing Info
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Billing Information:', 0, 1);
    $pdf->Cell(0, 10, 'Address: ' . $address, 0, 1);
    $pdf->Cell(0, 10, 'Postal Code: ' . $postalCode, 0, 1);
    $pdf->Cell(0, 10, 'Phone Number: ' . $phoneNumber, 0, 1);

    // Line break
    $pdf->Ln(10);

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(90, 10, 'Game', 1);
    $pdf->Cell(20, 10, 'Qty', 1);
    $pdf->Cell(40, 10, 'Price', 1);
    $pdf->Cell(40, 10, 'Total', 1);
    $pdf->Ln();

    // Table data
    foreach ($cart_items as $item) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(90, 10, $item['Title'], 1);
        $pdf->Cell(20, 10, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 10, '$' . number_format($item['priceAtAddition'], 2), 1);
        $pdf->Cell(40, 10, '$' . number_format($item['total'], 2), 1);
        $pdf->Ln();
    }

    // Total amount
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'Total:', 1);
    $pdf->Cell(40, 10, '$' . number_format($total_amount, 2), 1);
    $pdf->Ln();

    // Output the PDF (force download)
    $pdf->Output('D', 'invoice.pdf');

    exit();
} else {
    echo "Payment failed. Please try again.";
}

$conn->close();
