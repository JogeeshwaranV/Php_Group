<?php
require_once "dbinit.php";
// Initialize variables and error messages
$username_err = $email_err = $password_err = $firstname_err = $lastname_err = "";
$success_msg = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "gamestore";

    // Create connection
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize input values
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    // Validate inputs
    $valid = true;

    if (empty($username)) {
        $username_err = "Username is required.";
        $valid = false;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Valid email is required.";
        $valid = false;
    }

    if (empty($password)) {
        $password_err = "Password is required.";
        $valid = false;
    }

    if (empty($firstname)) {
        $firstname_err = "First name is required.";
        $valid = false;
    }

    if (empty($lastname)) {
        $lastname_err = "Last name is required.";
        $valid = false;
    }

    // Proceed if all inputs are valid
    if ($valid) {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO Users (Username, Email, Password, FirstName, LastName) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $firstname, $lastname);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to login.php after successful registration
            header("Location: login.php");
            exit();
        } else {
            $error_msg = "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
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
        <h2>User Registration</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
                <?php if (!empty($username_err)) echo '<div class="alert alert-danger">' . $username_err . '</div>'; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
                <?php if (!empty($email_err)) echo '<div class="alert alert-danger">' . $email_err . '</div>'; ?>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
                <?php if (!empty($password_err)) echo '<div class="alert alert-danger">' . $password_err . '</div>'; ?>
            </div>

            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname">
                <?php if (!empty($firstname_err)) echo '<div class="alert alert-danger">' . $firstname_err . '</div>'; ?>
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname">
                <?php if (!empty($lastname_err)) echo '<div class="alert alert-danger">' . $lastname_err . '</div>'; ?>
            </div>

            <input type="submit" value="Register">
            <?php if (!empty($success_msg)) echo '<div class="alert alert-success">' . $success_msg . '</div>'; ?>
            <?php if (!empty($error_msg)) echo '<div class="alert alert-danger">' . $error_msg . '</div>'; ?>
        </form>
        <p>Already Registered? <a href="login.php">Login Here</a></p>
    </div>
</body>

</html>