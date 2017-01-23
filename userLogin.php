<?php

require ('functions.inc');
$email = htmlentities($_POST["email"]);
$password = htmlentities($_POST["password"]);
$returnValue = array();

if(empty($email) || empty($password))
{
    $returnValue["status"] = "error";
    $returnValue["message"] = "Missing required field";
    echo json_encode($returnValue);
    return;
};

$user = new User;

if ($user->authenticate($_POST['email'],$_POST['password'])) {
    $returnValue["status"] = "Success";
    $returnValue["message"] = "User is registered";
    $returnValue["userInfo"] = getInfoOnUser($_POST['email']);
    echo json_encode($returnValue);
    return true;
}
else {

    $returnValue["status"] = "error";
    $returnValue["message"] = "User is not found";
    echo json_encode($returnValue);
}

function getInfoOnUser($email) {
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);

    if ($mysqli->connect_errno) {
        echo "error connecting to database in getInfoOnUser()";
    } else {
        $query = "SELECT * FROM Population where email='{$email}'";
        if (!$result = $mysqli->query($query)) {
            $retunValue = array("error" => "Cannot retrieve account for {$email}");
            return $retunValue;
        }
        //Will be only one row, so no while() loop needed
        $row = $result->fetch_assoc();

        $retunValue = array();
        $retunValue["name"] = $row['name'];
        $retunValue["email"] = $row['email'];
        $retunValue["registrationType"] = $row['registrationType'];

        return $retunValue;
    }
}

?>