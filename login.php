<?php
require_once ("functions.inc");
?>
<!doctype html>
<html>
<head>
    <script type="text/javascript" src="jquery-3.1.1.js"> </script>
    <script type="text/javascript" src="login.js"></script>
    <link rel="stylesheet" type="text/css" href="form.css">
    <title>Login</title>
</head>
<body>
    <form id="loginForm" method="POST" action="login-process.php">
        <div>
            <fieldset>
                <legend>
                    Login
                </legend>
                <div id="errorDiv">
                    <?php
                        if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                            unset($_SESSION['formAttempt']);
                            print "Errors Encountered<br />";
                            foreach ($_SESSION['error'] as $error) {
                                print $error . "<br />";
                            }
                        }
                    ?>
                </div>
                <label for="email">E-mail Address:* </label>
                <input type="text" id="email" name="email">
                <span class="errorFeedback errorSpan" id="emailError">E-mail is required</span>
                <br />
                <label for="password">Password:* </label>
                <input type="password" id="password" name="password">
                <span class="errorFeedback errorSpan" id="passwordError">Password required</span>
                <br />
                <input type="submit" id="submit" name="submit">
            </fieldset>
        </div>
    </form>
</body>
</html>
