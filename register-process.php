<?php
/**
 * Created by PhpStorm.
 * User: richardpoutier
 * Date: 12/31/16
 * Time: 5:14 PM
 */

require_once ('functions.inc');


$_SESSION['formAttempt'] = true;

if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
}
$_SESSION['error'] = array();

$required = array("name","email","password1","password2","registrationType");

//Check required fields
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $_SESSION['error'][] = $requiredField . " is required.";
    }
}

//validates the name
if (!preg_match('/^[\w .]+$/',$_POST['name'])){
    $_SESSION['error'][] = "Name must be letters and numbers only.";
}

//validates the radio button input
$validOptions = array("Student", "Professor");
if (!isset($_POST['registrationType']) && $_POST['registrationType'] != "") {
    if (!in_array($_POST['registrationType']) && $_POST['registrationType'] != "") {
        $_SESSION['error'][] = "Please choose a valid state.";
    }
}

//validate email address
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = "Invalid e-mail address.";
}

//validate passwords match
if ($_POST['password1'] != $_POST['password2']) {
    $_SESSION['error'][] = "Passwords don't match.";
}

//final diposition
if (isset($_SESSION['error']) && count($_SESSION['error']) > 0) {
    foreach ($_SESSION['error'] as  $error) {
        $response = array("message" => $error);

    } //end foreach
    sendResponse(400, json_encode($response));
    die(header("Location: register.php"));
    return false;

} else {
    $user = new User();
    if (registerUser($_POST)) {
        if ($user->authenticate($_POST['email'],$_POST['password'])) {
            $returnValue["status"] = "Success";
            $returnValue["message"] = "User is registered";
            $returnValue["userInfo"] = getInfoOnUser($_POST['email']);
            echo json_encode($returnValue);
            return true;
        }
        unset($_SESSION['formAttempt']);
        // Return unlock code, encoded with JSON
        $result = array(
            "message" => "{$_POST['email']} registered"
        );
        die(header("Location: success.php"));

        sendResponse(200, json_encode($result));
        return true;
    } else {
        $response = array("message" =>"Problem registering account");
        error_log("Problem registering user: {$_POST['email']}");
        $_SESSION['error'][] = "Problem registering account";
        sendResponse(400, json_encode($response));
        die(header("Location: register.php"));
        return false;
    }
}

function dieTo($value) {
    die(header($value));
}

function registerUser($userDate) {
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);
    if ($mysqli->connect_errno) {
        error_log("Cannot connect to MySQL: " . $mysqli->connect_errno);
        return false;
    }
    $email = $mysqli->real_escape_string($_POST['email']);

    //check for an existing user
    $findUser = "SELECT userId FROM Population WHERE email = '{$email}'";
    $findResult = $mysqli->query($findUser);
    $findRow = $findResult->fetch_assoc();
    if (isset($findRow['id']) && $findRow['id'] != "") {
        $_SESSION['error'][] = "A user with that e-mail address already exists";
        return false;
    }

    $name = $mysqli->real_escape_string($_POST['name']);
    //$firstName = $mysqli->real_escape_string($_POST['fname']);

    $cryptedPassword = crypt($_POST['password1']);
    $password = $mysqli->real_escape_string($cryptedPassword);


    if (isset($_POST['registrationType'])) {
        $registrationType = $mysqli->real_escape_string($_POST['registrationType']);
    } else {
        $registrationType = "";
    }

    $query2 = "SELECT * FROM population WHERE email='{$email}'";
    $result = $mysqli->query($query2);
    if($result->num_rows >= 1)
    {
        echo"name already exists";
        $val['error'] = "User account already exists with that email";
        die(header("Location: register.php"));
        sendResponse(406, json_encode($val));
        return false;
    }
    else {
        //insert query goes here


        $query = "INSERT INTO Population (name,email,password,registrationType) VALUES ('{$name}', '{$email}', '{$password}', '{$registrationType}')";
        if ($mysqli->query($query)) {
            $id = $mysqli->insert_id;
            error_log("Inserted {$email} as ID {$id}");
            return true;
        } else {
            error_log("Problem inserting {$query}");
            return false;
        }
    }

}   //end function registerUser

?>