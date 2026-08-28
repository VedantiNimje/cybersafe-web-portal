<?php

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

