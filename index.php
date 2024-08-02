<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

// Fetch user details from the database
$userId = $_SESSION["user"]; // User ID from session

$sql = "SELECT fullname FROM users WHERE id = ?"; // Use the correct column name for user ID
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Statement preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fullname);
mysqli_stmt_fetch($stmt);

if ($fullname) {
    $greeting = "Welcome, $fullname!";
} else {
    $greeting = "Welcome!";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        a {
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        a:hover {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <h1><?php echo $greeting; ?></h1>
    <a href="logout.php">Log Out</a>
</body>
</html>
