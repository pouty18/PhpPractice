<?php
/**
 * Created by PhpStorm.
 * User: richardpoutier
 * Date: 1/15/17
 * Time: 5:08 PM
 */

require_once ('functions.inc');

$email = htmlentities($_POST['email']);
$returnValue = array();

$required = array("type");

//Check required fields
$returnValue['error'] = array();
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $returnValue['error']['missingField'] = $requiredField . " is required.";
        echo json_encode($returnValue);
        return;
    }
}

//request general info about the user
if ($_POST['type'] == "getUserInfo") {
    if (userExists($_POST['email'])) {
        $returnValue["status"] = "Success";
        $returnValue["message"] = "User is registered";
        $returnValue["userInfo"] = getInfoOnUser($_POST['email']);
        echo json_encode(($returnValue));
        return true;
    } else {
        $response = array("message" => "Problem registering account");
        error_log("Problem registering user: {$_POST['email']}");
        $_SESSION['error'][] = "Problem registering account";
        echo json_encode(($response));
        sendResponse(400, json_encode($response));
        return false;
    }
}

function userExists($email) {
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);
    if ($mysqli->connect_errno) {
        echo "error connecting to database in getInfoOnUser()";
    } else {
        $query = "SELECT * FROM Population where email='{$email}'";
        if (!$result = $mysqli->query($query)) {
            return false;
        } else {
            return true;
        }
    }
}

function getInfoOnUser($email) {
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);

    if ($mysqli->connect_errno) {
        echo "error connecting to database in getInfoOnUser()";
    } else {
        $query = "SELECT * FROM Population where email='{$email}'";
        if (!$result = $mysqli->query($query)) {
            $value = array("error" => "Cannot retrieve account for {$email}");
            return $value;
        }
        //Will be only one row, so no while() loop needed
        $row = $result->fetch_assoc();

        $value = array();
        $value["name"] = $row['name'];
        $value["email"] = $row['email'];
        $value["registrationType"] = $row['registrationType'];

        return $value;
    }
}

?>

?>