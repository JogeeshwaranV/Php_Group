<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .containerr {
            width: 600px;
            margin: auto;
          
            padding: 50px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: #343a40;
            color:#ffcc00 !important;
        }
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
              
            color:#ffcc00 !important;
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
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
          
            font-size: 1.5rem;
            font-size: 1.5rem;
            color:#ffcc00;
        }


        input[type="submit"] {
           
       
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            background-color: #ffcc00;
          
          
        }

        input[type="submit"]:hover {
        
            background-color:#343a40 !important;
            color: #ffcc00;
            border: 1px solid #ffcc00;
        }

        .alert {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
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


    <div class="containerr">

        <?php
        if (isset($_POST["Register"])) {

            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["confirm-password"];

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $errors = array();

            if (empty($fullname) || empty($email) || empty($password) || empty($passwordRepeat)) {
                array_push($errors, "All fields are required to submit");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid");
            }
            if (strlen($password) < 8) {
                array_push($errors, "Password must be at least 8 characters long");
            }
            if ($password !== $passwordRepeat) {
                array_push($errors, "Passwords do not match");
            }
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                array_push($errors, "Email already exists");
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $password_hash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                } else {
                    die("Something went wrong");
                }
            }
        }
        ?>

        <h2>Register</h2>

        <form action="register.php" method="post">

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="confirm-password">Re-enter Password</label>
                <input type="password" id="confirm-password" name="confirm-password">
            </div>
            <input type="submit" value="Register" name="Register">

        </form>

        <div>
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>

    </div>

</body>

</html>