<?php

require_once ('functions.inc');

////prevent access if they haven't submitted the form
//if (!isset($_POST['submit'])) {
//    die(header("Location: login.php"));
//}

$name = $_POST['gameName'];
$walletSize = $_POST['walletSize'];
$multiplier = $_POST['multiplier'];

$_SESSION['formAttempt'] = true;

if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
$_SESSION['error'] = array();
$returnValue = array();

$required = array("gameName","walletSize", "multiplier");

//Check required fields
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $_SESSION['error'][] = $requiredField . " is required.";
        $returnValue['error'][] = $requiredField . "is required.";
    }
}

//Check if that user already submitted data for the given game, (no player can go twice on the same game)
//...
//...

$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);
if ($mysqli->connect_errno) {
    error_log("Cannot connect to MySQL: " . $mysqli->connect_errno);
    print "<div>error in make-game-process.php line 35</div>";
    return false;
}

$safeName = $mysqli->real_escape_string($name);
$query = "SELECT * FROM PublicGoodGames WHERE gameName = '{$safeName}'";
$result = $mysqli->query($query);
$count = $result->num_rows;

if (!$result) {
    error_log("Cannot retrieve account for {$user}");
    print("error, game name already exists");
    return false;
}

$insertQuery = "INSERT INTO PublicGoodGames (gameName,walletSize,multiplier) VALUES ('$name',$walletSize,$multiplier)";
if ($mysqli->query($insertQuery)) {
    $id = $mysqli->insert_id;
    error_log("Inserted {$name} as ID {$id}");
    print "$name, $walletSize, $multiplier";
    return true;
} else {
    error_log("Problem inserting {$query}");
    print  "We gotta problem";
    return false;
}


?>