<?php

session_start();

require_once "config/database.php";


// Make sure user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// Get the logged-in user's ID
$user_id = $_SESSION["user_id"];


// Get user information from database
$stmt = $conn->prepare(
    "SELECT id, name, email, profile_picture, created_at
     FROM users_details
     WHERE id = ?"
);

if ($stmt === false) {

    die("SQL Prepare Error: " . $conn->error);

}

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();


// Check user exists
if ($result->num_rows !== 1) {

    session_destroy();

    header("Location: login.php");
    exit();

}


$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Profile - CyberSafe</title>


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

        }


        .profile-container {

            max-width: 600px;

            margin: 60px auto;

            padding: 20px;

        }


        .profile-card {

            background: white;

            padding: 40px;

            border-radius: 15px;

            text-align: center;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.1);

        }


        .profile-avatar {
    width: 100px;
    height: 100px;

    min-width: 100px;
    min-height: 100px;

    border-radius: 50%;

    background: #2563eb;

    color: white;

    display: flex;
    align-items: center;
    justify-content: center;

    margin: 0 auto 20px;

    font-size: 40px;

    font-weight: bold;

    overflow: hidden;
}

.profile-avatar img {
    width: 100px;
    height: 100px;

    min-width: 100px;
    min-height: 100px;

    border-radius: 50%;

    object-fit: cover;

    display: block;
}
        h1 {

            margin-bottom: 10px;

        }


        .email {

            color: #666;

            margin-bottom: 30px;

        }


        .info {

            text-align: left;

            border-top: 1px solid #eee;

            padding-top: 20px;

        }


        .info-row {

            padding: 12px 0;

            border-bottom: 1px solid #eee;

        }


        .label {

            font-weight: bold;

            display: block;

            margin-bottom: 5px;

        }


        .value {

            color: #555;

        }


        .buttons {

            margin-top: 30px;

        }


        .button {

            display: inline-block;

            padding: 12px 20px;

            margin: 5px;

            border-radius: 8px;

            text-decoration: none;

            background: #2563eb;

            color: white;

        }


        .button:hover {

            background: #1d4ed8;

        }


        .back {

            background: #555;

        }


        .back:hover {

            background: #333;

        }

    </style>

</head>


<body>


<div class="profile-container">

    <div class="profile-card">


        <div class="profile-avatar">

    <?php if (!empty($user["profile_picture"])): ?>

        <img
            src="<?php echo htmlspecialchars($user["profile_picture"]); ?>"
            alt="Profile Picture"
        >

    <?php else: ?>

        <?php

        echo strtoupper(
            substr($user["name"], 0, 1)
        );

        ?>

    <?php endif; ?>

</div>


        <h1>

            <?php

            echo htmlspecialchars($user["name"]);

            ?>

        </h1>


        <p class="email">

            <?php

            echo htmlspecialchars($user["email"]);

            ?>

        </p>


        <div class="info">


            <div class="info-row">

                <span class="label">
                    Name
                </span>

                <span class="value">

                    <?php

                    echo htmlspecialchars($user["name"]);

                    ?>

                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Email
                </span>

                <span class="value">

                    <?php

                    echo htmlspecialchars($user["email"]);

                    ?>

                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Account Created
                </span>

                <span class="value">

                    <?php

                    echo htmlspecialchars($user["created_at"]);

                    ?>

                </span>

            </div>


        </div>


        <div class="buttons">

    <a
        href="edit-profile.php"
        class="button">

        
    </a>

    <a
        href="index.php"
        class="button back">

       
    </a>

    <a
        href="logout.php"
        class="button">

        Logout

    </a>

</div>


    </div>

</div>


</body>

</html>