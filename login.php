<?php
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gamestore";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT UserID, Password FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);

    // Execute the query
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user'] = $userID;
            
            // Redirect to home.php
            header("Location: home.php");
            exit();
        } else {
            $error_msg = "Incorrect password.";
        }
    } else {
        $error_msg = "No account found with that email.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 600px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: #343a40;
            color: #ffcc00;
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #ffcc00;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: #fff;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #ffcc00;
        }

        input[type="submit"] {
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            background-color: #ffcc00;
            border: none;
            color: #343a40;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #343a40;
            color: #ffcc00;
            border: 1px solid #ffcc00;
        }

        .alert {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        p {
            text-align: center;
            margin-top: 15px;
            color: #ffcc00;
        }

        a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #ffcc00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Login</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="submit" value="Login">
            <?php if (!empty($error_msg)) echo '<div class="alert alert-danger">' . $error_msg . '</div>'; ?>
        </form>
        <p>Not registered? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>
