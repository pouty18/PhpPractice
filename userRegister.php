<?php

require_once ('functions.inc');
$returnValue = array();

$email = htmlentities($_POST["email"]);
$password = htmlentities($_POST["password"]);

$required = array("name","email","password1","password2","registrationType");

//Check required fields
$returnValue['error'] = array();
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $returnValue['error']['missingField'] = $requiredField . " is required.";
        echo json_encode($returnValue);
        return;
    }
}

//validates the name
if (!preg_match('/^[\w .]+$/',$_POST['name'])){
    $returnValue['error']['nameError'] = "Name must be letters and numbers only.";
}

//validate email address
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $returnValue['error']['emailError'] = "Invalid e-mail address.";
}

//validate passwords match
if ($_POST['password1'] != $_POST['password2']) {
    $returnValue['error']['passwordError'] = "Passwords don't match.";
}


if (registerUser($_POST)) {
    $returnValue["status"] = "Success";
    $returnValue["message"] = "User is blah blah why isn't this working";
    $returnValue["userInfo"] = getInfoOnUser($_POST['email']);
    echo json_encode(($returnValue));
    return true;
}   else {
            $returnValue["status"] = "Error";
            $returnValue["message"] = "Email already registered";
            echo json_encode($returnValue);
            return false;
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
//    $findResult = $mysqli->query($findUser);
//    if($findResult->num_rows >= 1) {
//        //a user already exists with login info
//        return false;
//    }
    $findResult = $mysqli->query($findUser);
    $findRow = $findResult->fetch_assoc();
    if (isset($findRow['id']) && $findRow['id'] != "") {
        $_SESSION['error'][] = "A user with that e-mail address already exists";
        return false;
    } else {
        $name = $mysqli->real_escape_string($_POST['name']);
        //$firstName = $mysqli->real_escape_string($_POST['fname']);

        $cryptedPassword = crypt($_POST['password1']);
        $password = $mysqli->real_escape_string($cryptedPassword);


        if (isset($_POST['registrationType'])) {
            $registrationType = $mysqli->real_escape_string($_POST['registrationType']);
        } else {
            $registrationType = "";
        }

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