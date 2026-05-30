<?php
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

$userID = (int)($_SESSION["userID"] ?? 0);
$accountID = (int)($_SESSION["accountID"] ?? 0);
$userName = $_SESSION["userName"] ?? "unknown";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>

<img src="logo.png" alt="Geometry Dash" style="max-width:350px;"><br>

<body style="background:#b3b3b3; font-family: serif;">

    <p>Logged in as: <b><?php echo htmlspecialchars($userName); ?></b></p>
    <p>User ID: <b><?php echo $userID; ?></b> | Account ID: <b><?php echo $accountID; ?></b></p>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>