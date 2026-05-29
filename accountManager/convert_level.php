<?php
session_start();
require __DIR__ . "/../incl/lib/connection.php";

$levelID = (int)($_POST["levelID"] ?? 0);

echo "LEVEL ID: " . $levelID . "<br>";

$q = $db->prepare("SELECT levelID, levelName, gameVersion, binaryVersion, userID, extID FROM levels WHERE levelID = :levelID");
$q->execute([':levelID' => $levelID]);
$row = $q->fetch(PDO::FETCH_ASSOC);

echo "<pre>BEFORE:\n";
print_r($row);
echo "</pre>";

$u = $db->prepare("UPDATE levels SET gameVersion = 19, binaryVersion = 26 WHERE levelID = :levelID");
$ok = $u->execute([':levelID' => $levelID]);

echo "UPDATE OK: ";
var_dump($ok);

echo "<pre>ERROR:\n";
print_r($u->errorInfo());
echo "</pre>";

$q = $db->prepare("SELECT levelID, levelName, gameVersion, binaryVersion, userID, extID FROM levels WHERE levelID = :levelID");
$q->execute([':levelID' => $levelID]);
$row = $q->fetch(PDO::FETCH_ASSOC);

echo "<pre>AFTER:\n";
print_r($row);
echo "</pre>";