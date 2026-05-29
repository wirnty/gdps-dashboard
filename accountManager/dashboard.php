<?php
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

require __DIR__ . "/../incl/lib/connection.php";

$userID = (int)($_SESSION["userID"] ?? 0);
$accountID = (int)($_SESSION["accountID"] ?? 0);
$userName = $_SESSION["userName"] ?? "unknown";

$msg = $_SESSION["convert_msg"] ?? "";
unset($_SESSION["convert_msg"]);

$query = $db->prepare("
    SELECT levelID, levelName, gameVersion, binaryVersion, userID, extID, uploadDate
    FROM levels
    WHERE userID = :userID OR extID = :accountID
    ORDER BY levelID DESC
    LIMIT 100
");
$query->execute([
    ':userID' => $userID,
    ':accountID' => $accountID
]);
$levels = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level Converter</title>
</head>
    <img src="logo.png" alt="Geometry Dash" style="max-width:350px;"><br>
<body style="background:#b3b3b3; font-family: serif;">

    <h1>Level Converter</h1>
    <p>Logged in as: <b><?php echo htmlspecialchars($userName); ?></b></p>
    <p>User ID: <b><?php echo $userID; ?></b> | Account ID: <b><?php echo $accountID; ?></b></p>

    <?php if ($msg): ?>
        <p><b><?php echo htmlspecialchars($msg); ?></b></p>
    <?php endif; ?>

    <hr>

    <h2>Convert level to 1.9</h2>
    <form method="post" action="convert_level.php">
        <p>Level ID:</p>
        <input type="number" name="levelID" required>
        <br><br>

        <label>
            <input type="checkbox" name="clear_new_fields" value="1" checked>
            Clear newer fields
        </label>

        <br><br>
        <button type="submit">Convert to 1.9</button>
    </form>

    <hr>

    <h2>Your levels</h2>

    <?php if (!$levels): ?>
        <p>No levels found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Level ID</th>
                <th>Name</th>
                <th>Game Version</th>
                <th>Binary Version</th>
                <th>User ID</th>
                <th>Account ID</th>
                <th>Action</th>
            </tr>
            <?php foreach ($levels as $level): ?>
                <tr>
                    <td><?php echo (int)$level["levelID"]; ?></td>
                    <td><?php echo htmlspecialchars($level["levelName"]); ?></td>
                    <td><?php echo htmlspecialchars((string)$level["gameVersion"]); ?></td>
                    <td><?php echo htmlspecialchars((string)$level["binaryVersion"]); ?></td>
                    <td><?php echo htmlspecialchars((string)$level["userID"]); ?></td>
                    <td><?php echo htmlspecialchars((string)$level["extID"]); ?></td>
                    <td>
                        <form method="post" action="convert_level.php" style="margin:0;">
                            <input type="hidden" name="levelID" value="<?php echo (int)$level["levelID"]; ?>">
                            <input type="hidden" name="clear_new_fields" value="1">
                            <button type="submit">Convert</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>