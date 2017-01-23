<?php

class User {
    public $userId;
    public $name;
    public $email;
    public $registrationType;
    public $isLoggedIn = false;

    function __construct() {
        if (session_id() == "") {
            session_start();
        }
        if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] == true) {
            $this->_initUser();
        }
    } // end __contruct

    public function authenticate($user, $pass) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);

        if ($mysqli->connect_errno) {
            error_log("Cannot connect to MySQL: " . $mysqli->connect_errno);
            print "<div>HERE</div>";
            return false;
        }

        $safeUser = $mysqli->real_escape_string($user);
        $incomingPassword = $mysqli->real_escape_string($pass);
        $query = "SELECT * FROM Population WHERE email = '{$safeUser}'";

        if (!($result = $mysqli->query($query))) {
            error_log("Cannot retrieve account for {$user}");
            return false;
        }

        if ($mysqli->query($query)->num_rows == 0 ) {
            error_log("no information available for account");
            return false;
        } else {
            //Will be only one row, so no while() loop needed
            $row = $result->fetch_assoc();
            $dbPassword = $row['password'];

            if (crypt($incomingPassword, $dbPassword) != $dbPassword) {
                error_log("Passwords for {$user} don't match");
                return false;
            }
            $this->userId = $row['userId'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->registrationType = $row['registrationType'];
            $this->isLoggedIn = true;

            $this->_setSession();

            return true;
        }
    }

    private function _setSession() {
        if (session_id() == '') {
            session_start();
        }

        $_SESSION['userId'] = $this->userId;
        $_SESSION['email'] = $this->email;
        $_SESSION['name'] = $this->name;
        $_SESSION['registrationType'] = $this->registrationType;
        $_SESSION['isLoggedIn'] = $this->isLoggedIn;

    } //end function setSession

    private function _initUser() {
        if (session_id() == '') {
            session_start();
        }

        $this->userId = $_SESSION['userId'];
        $this->email = $_SESSION['email'];
        $this->name = $_SESSION['name'];
        $this->registrationType = $_SESSION['registrationType'];
        $this->isLoggedIn = $_SESSION['isLoggedIn'];
    } // end function initUser


    public function logout() {
        $this->isLoggedIn = false;

        if (session_id() == '') {
            session_start();
        }

        $_SESSION['IsLoggedIn'] = false;
        foreach ($_SERVER as $key => $value) {
            $_SESSION[$key] = "";
            unset($_SESSION[$key]);
        }

        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $cookieParameters = session_get_cookie_params();
            setcookie(session_name(), '', time() - 28800, $cookieParameters['path'],$cookieParameters['domain'], $cookieParameters['secure'], $cookieParameters['httponly']);
        }

        session_destroy();

    }
} // end class user