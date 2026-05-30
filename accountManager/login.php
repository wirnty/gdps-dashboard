<?php
session_start();

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["login_error"] ?? "";
unset($_SESSION["login_error"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Manager</title>
</head>
<body style="background:#b3b3b3; font-family: serif;">

    <img src="logo.png" alt="Geometry Dash" style="max-width:350px;"><br>
    <h2>Account Manager</h2>

    <fieldset>
        <legend>Please login</legend>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="do_login.php">
            <p>Username:</p>
            <input type="text" name="userName" required>

            <p>Password:</p>
            <input type="password" name="password" required>

            <br><br>
            <button type="submit">Login</button>
        </form>

        <ul>
            <li><a href="register.php">Register</a></li>
        </ul>
    </fieldset>

</body>
</html>
