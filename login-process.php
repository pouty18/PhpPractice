<?php

require_once ('functions.inc');

////prevent access if they haven't submitted the form
//if (!isset($_POST['submit'])) {
//    die(header("Location: login.php"));
//}

$_SESSION['formAttempt'] = true;

if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
$_SESSION['error'] = array();
$returnValue = array();

$required = array("email", "password");

//Check required fields
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $_SESSION['error'][] = $requiredField . " is required.";
        $returnValue['error'][] = $requiredField . "is required.";
    }
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = "Invalid e-mail address.";
    $returnValue['error'][] = "Invalid e-mail address.";
}

if (count($_SESSION['error']) > 0) {
    $result = array("message" => "unsuccessful login");
    die(header("Location: login.php"));
    sendResponse(400, json_encode($result));
    return false;
} else {
    $user = new User;

    if ($user->authenticate($_POST['email'],$_POST['password'])) {
        unset($_SESSION['formAttempt']);
        $result = array("message" => "successful login");
        die(header("Location: authenticated.php"));
        sendResponse(200, json_encode($result));
        return true;
    } else {
        $_SESSION['error'][] = "ERROR: EMAIL: {$_POST['email']}, PASSWORD: {$_POST['password']}";
        $result = array("message" => "error logging in");
        die(header("Location: login.php"));
        sendResponse(400, json_encode($result));

        return false;
    }

}

?>