<?php

require_once ('functions.inc');

class iosApi {
    private $db;

    function __construct()
    {
        $this->db = new mysqli(DBHOST, DBUSER, DBPASS, DB);
        $this->db->autocommit(FALSE);

    }

    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }

    function setUp() {

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

        //validate email address
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'][] = "Invalid e-mail address.";
        }

        if ($_POST['password1'] != $_POST['password2']){
            $_SESSION['error'][] = "Password's don't match.";
        }

    }

    function run()
    {
        $this->setUp();

        if (isset($_SESSION['error']) && count($_SESSION['error']) > 0) {
            // Return unlock code, encoded with JSON
            if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                unset($_SESSION['formAttempt']);

                foreach ($_SESSION['error'] as  $error) {
                    $response = array("message" => $error);

                } //end foreach
            }
            sendResponse(400, json_encode($response));
            return false;
        } else {
            if ($this->registerUser($_POST)) {
                // Return unlock code, encoded with JSON
                $result = array(
                    "message" => "{$_POST['email']} registered"
                );
                sendResponse(200, json_encode($result));
                return true;
            } else {

                //PROBLEM REGISTERING USER
                error_log("Problem registering user: {$_POST['email']}");
                $_SESSION['error'][] = "Problem registering account";
                die(header("Location: register.php"));
            }
        }
        // Print all codes in database
        $stmt = $this->db->prepare('SELECT userId, name, email FROM Population');
        $stmt->execute();
        $stmt->bind_result($userId, $name, $email);
        while ($stmt->fetch()) {
            echo "User $userId: $name - $email<br />";
        }
        $stmt->close();
    }

    function registerUser($userData) {
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
//            $_SESSION['error'][] = "A user with that e-mail address already exists";
            sendResponse(403, 'E-mail already used');
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

}

$api = new iosApi();
$api->run();



?>