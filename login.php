
<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

if (isset($_POST["Login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    require_once "database.php";

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT id, fullname, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId, $fullname, $hashedPassword);
    mysqli_stmt_fetch($stmt);

    if ($userId) {
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user"] = $userId; // Store user ID in session
            header("Location: home.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Password does not match</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Email does not match</div>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-color: #f8f9fa;
          display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .containerl {
          /*   max-width: 500px; */
            width: 600px;
           /*  width: 100%; */
            padding: 2rem;
            padding-top: 4rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
            background-color: #343a40;
            color:#ffcc00 !important;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color:#ffcc00;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            
            color:#ffcc00 !important;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            box-sizing: border-box;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
         
            background-color:#ffcc00 !important;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
            text-align: center;
        }

        .btn-primary:hover {
            background-color:#343a40 !important;
            color: #ffcc00;
            border: 1px solid #ffcc00;
        }

        .alert {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid transparent;
            border-radius: 0.5rem;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        p {
            text-align: center;
            margin-top: 1.5rem;
        }

        a {
            color: white;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="containerl">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="Login" class="btn-primary">Login</button>
        </form>
        <p>Not Registered Yet? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>
