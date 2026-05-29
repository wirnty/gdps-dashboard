<?php
session_start();

require __DIR__ . "/../config/security.php";
require __DIR__ . "/../config/mail.php";
require __DIR__ . "/../incl/lib/connection.php";
require_once __DIR__ . "/../incl/lib/mainLib.php";
require_once __DIR__ . "/../incl/lib/exploitPatch.php";
require_once __DIR__ . "/../incl/lib/generatePass.php";
require_once __DIR__ . "/../incl/lib/automod.php";

$gs = new mainLib();

if (Automod::isAccountsDisabled(0)) {
    $_SESSION["register_error"] = "Registration is disabled.";
    header("Location: register.php");
    exit;
}

if (!isset($preactivateAccounts)) $preactivateAccounts = true;
if (!isset($filterUsernames)) global $filterUsernames;

if (empty($_POST["userName"]) || empty($_POST["password"]) || empty($_POST["email"])) {
    $_SESSION["register_error"] = "Fill all fields.";
    header("Location: register.php");
    exit;
}

$userName = str_replace(' ', '', ExploitPatch::charclean($_POST["userName"]));
$password = $_POST["password"];
$email = ExploitPatch::rucharclean($_POST["email"]);

if ($filterUsernames >= 1) {
    $bannedUsernamesList = array_map('strtolower', $bannedUsernames);
    switch ($filterUsernames) {
        case 1:
            if (in_array(strtolower($userName), $bannedUsernamesList)) {
                $_SESSION["register_error"] = "Username is not allowed.";
                header("Location: register.php");
                exit;
            }
            break;
        case 2:
            foreach ($bannedUsernamesList as $bannedUsername) {
                if (!empty($bannedUsername) && mb_strpos(strtolower($userName), $bannedUsername) !== false) {
                    $_SESSION["register_error"] = "Username is not allowed.";
                    header("Location: register.php");
                    exit;
                }
            }
            break;
    }
}

if (strlen($userName) > 20) {
    $_SESSION["register_error"] = "Username is too long.";
    header("Location: register.php");
    exit;
}

if (strlen($userName) < 3) {
    $_SESSION["register_error"] = "Username is too short.";
    header("Location: register.php");
    exit;
}

if (strlen($password) < 6) {
    $_SESSION["register_error"] = "Password is too short.";
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["register_error"] = "Invalid email.";
    header("Location: register.php");
    exit;
}

if ($mailEnabled) {
    $checkMail = $db->prepare("SELECT count(*) FROM accounts WHERE email LIKE :mail");
    $checkMail->execute([':mail' => $email]);
    $checkMail = $checkMail->fetchColumn();

    if ($checkMail > 0) {
        $_SESSION["register_error"] = "Email already used.";
        header("Location: register.php");
        exit;
    }
}

$query2 = $db->prepare("SELECT count(*) FROM accounts WHERE userName LIKE :userName");
$query2->execute([':userName' => $userName]);
$regusrs = $query2->fetchColumn();

if ($regusrs > 0) {
    $_SESSION["register_error"] = "Username already exists.";
    header("Location: register.php");
    exit;
}

$hashpass = password_hash($password, PASSWORD_DEFAULT);
$gjp2 = GeneratePass::GJP2hash($password);

$query = $db->prepare("
    INSERT INTO accounts (userName, password, email, registerDate, isActive, gjp2)
    VALUES (:userName, :password, :email, :time, :isActive, :gjp)
");

$query->execute([
    ':userName' => $userName,
    ':password' => $hashpass,
    ':email' => $email,
    ':time' => time(),
    ':isActive' => $preactivateAccounts ? 1 : 0,
    ':gjp' => $gjp2
]);

$accountID = $db->lastInsertId();

$gs->logAction($accountID, 1, $userName, $email, $gs->getUserID($accountID, $userName));
$gs->sendLogsRegisterWebhook($accountID);

if ($mailEnabled) {
    $gs->mail($email, $userName);
}

$_SESSION["register_success"] = "Registration successful. Now login.";
header("Location: register.php");
exit;