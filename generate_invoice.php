<?php
session_start();
require_once './libs/fpdf/fpdf.php';

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
SELECT g.Title, ci.quantity, ci.priceAtAddition, (ci.quantity * ci.priceAtAddition) AS total
FROM CartItems ci
JOIN ShoppingCarts sc ON ci.cartID = sc.cartID
JOIN Games g ON ci.gameID = g.GameID
WHERE sc.userID = ? AND sc.purchased = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(40, 10, 'Game Store Invoice');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Game');
$pdf->Cell(30, 10, 'Quantity');
$pdf->Cell(40, 10, 'Price');
$pdf->Cell(40, 10, 'Total');
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(80, 10, $row['Title']);
    $pdf->Cell(30, 10, $row['quantity']);
    $pdf->Cell(40, 10, '$' . number_format($row['priceAtAddition'], 2));
    $pdf->Cell(40, 10, '$' . number_format($row['total'], 2));
    $pdf->Ln();
    $total_amount += $row['total'];
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'Total Amount:');
$pdf->Cell(40, 10, '$' . number_format($total_amount, 2));

$pdf->Output('D', 'invoice.pdf');

$conn->close();
