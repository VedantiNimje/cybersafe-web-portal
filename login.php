<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check empty fields
    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";
        $message_type = "error";

    }

    // Check email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }

    else {

        // Find user by email
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, profile_picture
             FROM users_details
             WHERE email = ?"
        );

        if ($stmt === false) {
            die("SQL Prepare Error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        // Check whether user exists
        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // Check password
            if (password_verify($password, $user["password"])) {

                // Login successful
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["profile_picture"] = $user["profile_picture"];

                // Send user to dashboard
                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Incorrect email or password.";
                $message_type = "error";
            }

        } else {

            $message = "Incorrect email or password.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - CyberSafe</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-box {
            background: white;
            padding: 35px;
            border-radius: 15px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: bold;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;

            border: 1px solid #ccc;
            border-radius: 8px;

            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .login-btn {
            width: 100%;
            padding: 13px;

            border: none;
            border-radius: 8px;

            background: #2563eb;
            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;

            border-radius: 8px;

            text-align: center;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <div class="logo">
            🛡️ CyberSafe
        </div>

        <p class="subtitle">
            Login to your account
        </p>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST"
              action="login.php">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <button
                type="submit"
                class="login-btn">

                Login

            </button>

        </form>


        <div class="signup-link">

            Don't have an account?

            <a href="signup.php">
                Create Account
            </a>

        </div>

    </div>

</div>

</body>

</html>
