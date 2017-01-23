<?php require_once("functions.inc"); ?>
<!doctype html>
<html>
<head>
    <script type="text/javascript" src="jquery-3.1.1.js"> </script>
    <script type="text/javascript" src="register.js"></script>
    <link rel="stylesheet" type="text/css" href="form.css">
    <title>A Form</title>
</head>
<body>
<form id="userForm" method="POST" action="register-process.php">
    <div>
        <fieldset>
            <legend>Registration Information</legend>
            <div id="errorDiv">
                <?php
                if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                    unset($_SESSION['formAttempt']);
                    print "Errors Encountered<br />";
                    foreach ($_SESSION['error'] as  $error) {
                        print $error . "<br />\n";
                    } //end foreach
                } //end if
                ?>
            </div>
            <label for="name">Name:* </label>
            <input type="text" id="name" name="name">
            <span class="errorFeedback errorSpan" id="nameError">Name is required</span>
            <br />
            <label for="email">E-mail Address:* </label>
            <input type="text" id="email" name="email">
            <span class="errorFeedback errorSpan" id="emailError">Email is required</span>
            <br />
            <label for=”password1”>Password:* </label>
            <input type="password" id="password1" name="password1">
            <span class="errorFeedback errorSpan" id="password1Error">Password required</span>
            <br />
            <label for="password2">Verify Password:* </label>
            <input type="password" id="password2" name="password2">
            <span class="errorFeedback errorSpan" id="password2Error">Passwords don't match</span>
            <br />
            <label for=”student”>Registration Type:</label>
            <input class="radioButton" type="radio" name="registrationType" id="student" value="Student">
            <label class="radioButton" for="student">Student</label>
            <input class="radioButton" type="radio" name="registrationType" id="professor" value="Professor">
            <label class="radioButton" for="professor">Professor</label>
            <span class="errorFeedback errorSpan phoneTypeError" id="RegistrationTypeError">Please choose an option</span>
            <br />

            <input type="submit" id="submit" name="submit">
        </fieldset>
    </div>
</form>
</body>
</html>