<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";


/* Get user information */

$stmt = $conn->prepare(
    "SELECT name, email, profile_picture
     FROM users_details
     WHERE id = ?"
);

if ($stmt === false) {

    die("Database error: " . $conn->error);

}

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/* Form submitted */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");


    if (empty($name) || empty($email)) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }

    else {

        $check = $conn->prepare(
            "SELECT id
             FROM users_details
             WHERE email = ?
             AND id != ?"
        );

        $check->bind_param("si", $email, $user_id);

        $check->execute();

        $check_result = $check->get_result();


        if ($check_result->num_rows > 0) {

            $message = "This email is already being used.";
            $message_type = "error";

        }

        else {

            $update = $conn->prepare(
                "UPDATE users_details
                 SET name = ?, email = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "ssi",
                $name,
                $email,
                $user_id
            );


            if ($update->execute()) {

                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $email;

                $user["name"] = $name;
                $user["email"] = $email;


                /* Profile picture */

                if (
                    isset($_FILES["profile_picture"]) &&
                    $_FILES["profile_picture"]["error"] === UPLOAD_ERR_OK
                ) {

                    $file_tmp = $_FILES["profile_picture"]["tmp_name"];
                    $file_size = $_FILES["profile_picture"]["size"];
                    $file_name = $_FILES["profile_picture"]["name"];


                    if ($file_size > 2 * 1024 * 1024) {

                        $message = "Profile picture must be less than 2 MB.";
                        $message_type = "error";

                    }

                    else {

                        $file_type = mime_content_type($file_tmp);

                        $allowed_types = [
                            "image/jpeg",
                            "image/png",
                            "image/webp"
                        ];


                        if (!in_array($file_type, $allowed_types)) {

                            $message = "Only JPG, PNG and WEBP images are allowed.";
                            $message_type = "error";

                        }

                        else {

                            $extension = strtolower(
                                pathinfo(
                                    $file_name,
                                    PATHINFO_EXTENSION
                                )
                            );


                            $new_file_name =
                                "user_" .
                                $user_id .
                                "_" .
                                time() .
                                "." .
                                $extension;


                            $upload_path =
                                "uploads/" .
                                $new_file_name;


                            if (
                                move_uploaded_file(
                                    $file_tmp,
                                    $upload_path
                                )
                            ) {

                                $picture_stmt = $conn->prepare(
                                    "UPDATE users_details
                                     SET profile_picture = ?
                                     WHERE id = ?"
                                );

                                $picture_stmt->bind_param(
                                    "si",
                                    $upload_path,
                                    $user_id
                                );

                                $picture_stmt->execute();

                                $picture_stmt->close();

                                $_SESSION["profile_picture"] =
                                    $upload_path;

                                $user["profile_picture"] =
                                    $upload_path;

                            }

                        }

                    }

                }


                if ($message === "") {

                    $message =
                        "Profile updated successfully!";

                    $message_type = "success";

                }

            }

            else {

                $message =
                    "Could not update your profile.";

                $message_type = "error";

            }

            $update->close();

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

    <title>Edit Profile - CyberSafe</title>


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


        .container {

            width: 100%;

            max-width: 500px;

            padding: 20px;

        }


        .card {

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.1);

        }


        h1 {

            text-align: center;

            margin-bottom: 10px;

        }


        .subtitle {

            text-align: center;

            color: #666;

            margin-bottom: 25px;

        }


        .message {

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;

        }


        .success {

            background: #dcfce7;

            color: #166534;

        }


        .error {

            background: #fee2e2;

            color: #991b1b;

        }


        .form-group {

            margin-bottom: 20px;

        }


        label {

            display: block;

            font-weight: bold;

            margin-bottom: 7px;

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


        .save-button {

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


        .save-button:hover {

            background: #1d4ed8;

        }


        .back {

            display: block;

            text-align: center;

            margin-top: 20px;

            color: #2563eb;

            text-decoration: none;

        }

    </style>

</head>


<body>


<div class="container">


    <div class="card">


        <h1>
            Edit Profile
        </h1>


        <p class="subtitle">
            Update your CyberSafe account information.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST"
      action="edit-profile.php"
      enctype="multipart/form-data">

    <!-- Profile Picture -->

    <div class="form-group">

        <label for="profile_picture">
            Profile Picture
        </label>

        <input
            type="file"
            id="profile_picture"
            name="profile_picture"
            accept="image/jpeg,image/png,image/webp"
        >

        <small>
            JPG, PNG or WEBP. Maximum size: 2 MB.
        </small>

    </div>


    <!-- Name -->

    <div class="form-group">

        <label for="name">
            Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($user["name"]); ?>"
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
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    required
                >

            </div>


            <button
                type="submit"
                class="save-button">

                Save Changes

            </button>


        </form>


        <a
            href="profile.php"
            class="back">

            ← Back to Profile

        </a>


    </div>

</div>


</body>

</html>