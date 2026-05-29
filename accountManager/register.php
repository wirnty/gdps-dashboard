<?php
session_start();

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["register_error"] ?? "";
$success = $_SESSION["register_success"] ?? "";
unset($_SESSION["register_error"], $_SESSION["register_success"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Geometry Dash Account Manager</title>
</head>
<body style="background:#b3b3b3; font-family: serif;">

    <img src="logo.png" alt="Geometry Dash" style="max-width:350px;"><br>
    <h2>Account Manager</h2>

    <fieldset>
        <legend>Please register</legend>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="post" action="do_register.php">
            <p>Username:</p>
            <input type="text" name="userName" required>

            <p>Password:</p>
            <input type="password" name="password" required>

            <p>Email:</p>
            <input type="email" name="email" required>

            <br><br>
            <button type="submit">Register</button>
        </form>

        <ul>
            <li><a href="login.php">Back to Login</a></li>
        </ul>
    </fieldset>

</body>
</html>