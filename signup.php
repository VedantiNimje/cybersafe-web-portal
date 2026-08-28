<?php

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check empty fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }

    // Check email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }

    // Check password length
    elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters long.";
        $message_type = "error";

    }

    // Check passwords
    elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    }

    else {

        // Check whether email already exists
        $check = $conn->prepare(
            "SELECT id FROM users_details WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            // Securely hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            
            $stmt = $conn->prepare(
    "INSERT INTO users_details (name, email, password)
     VALUES (?, ?, ?)"
                          );

               if ($stmt === false) {
                   die("SQL Prepare Error: " . $conn->error);
                          }

            $stmt->bind_param(
                "sss",
                $name, 
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Account created successfully! You can now log in.";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account - CyberSafe</title>

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

        .signup-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .signup-box {
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

        .signup-btn {
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

        .signup-btn:hover {
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

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="signup-container">

    <div class="signup-box">

        <div class="logo">
            🛡️ CyberSafe
        </div>

        <p class="subtitle">
            Create your account
        </p>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST"
              action="signup.php">

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >

            </div>


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
                    placeholder="Enter password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Enter password again"
                    required
                >

            </div>


            <button
                type="submit"
                class="signup-btn">

                Create Account

            </button>

        </form>


        <div class="login-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

    </div>

</div>

</body>

</html>