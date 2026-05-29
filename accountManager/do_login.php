<?php
session_start();

require __DIR__ . "/../incl/lib/connection.php";
require __DIR__ . "/../incl/lib/generatePass.php";
require_once __DIR__ . "/../incl/lib/exploitPatch.php";
require_once __DIR__ . "/../incl/lib/mainLib.php";

$gs = new mainLib();

$udid = ExploitPatch::remove($_POST["udid"] ?? "");
$userName = ExploitPatch::charclean($_POST["userName"] ?? "");
$passInput = $_POST["password"] ?? "";

$query = $db->prepare("SELECT accountID FROM accounts WHERE userName LIKE :userName");
$query->execute([':userName' => $userName]);

if ($query->rowCount() == 0) {
    $_SESSION["login_error"] = "User not found.";
    header("Location: login.php");
    exit;
}

$accountID = $query->fetchColumn();
$pass = 0;

if (!empty($passInput)) {
    $pass = GeneratePass::isValidUsrname($userName, $passInput);
}

if ($pass == 1) {
    $gs->logAction($accountID, 2);
    $userID = $gs->getUserID($accountID, $userName);

    if (!is_numeric($udid)) {
        $query2 = $db->prepare("SELECT userID FROM users WHERE extID = :udid");
        $query2->execute([':udid' => $udid]);
        $usrid2 = $query2->fetchColumn();

        if ($usrid2) {
            $query2 = $db->prepare("UPDATE levels SET userID = :userID, extID = :extID WHERE userID = :usrid2");
            $query2->execute([
                ':userID' => $userID,
                ':extID' => $accountID,
                ':usrid2' => $usrid2
            ]);
        }
    }

    $_SESSION["logged_in"] = true;
    $_SESSION["accountID"] = $accountID;
    $_SESSION["userID"] = $userID;
    $_SESSION["userName"] = $userName;

    header("Location: dashboard.php");
    exit;
}

if ($pass == '-1') {
    $_SESSION["login_error"] = "Wrong password.";
    header("Location: login.php");
    exit;
}

$_SESSION["login_error"] = "Login failed.";
header("Location: login.php");
exit;