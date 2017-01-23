<?php

require_once ("functions.inc");

$user = new User;
if (!$user->isLoggedIn) {
    die(header("Location: login.php"));
}

?>
<!doctype html>
<html>
<head>
    <script type="text/javascript" src="jquery-3.1.1.js"> </script>
    <link rel="stylesheet" type="text/css" href="form.css">
    <title>Super Secret Authenticated Page</title>
</head>
<body>
<div>
    <span class="homePage" id="homePageMainTitle" > <?php print "{$user->name}" ?> </span>
    <br />
    <div>
        Welcome to you're very first webpage!
    </div>
    <div>
        <?php
            if (strtolower($user->registrationType) == "professor") {
                print "Please select a game from the options below: ";
            }
            if (strtolower($user->registrationType) == "student") {
                print "Please choose which game you want to play: ";
                print "<br />
                        <form id=\"createPublicGoodGameForm\" method=\"POST\" action=\"make-game-process.php\">
                            <div>
                                <fieldset>
                                    <legend>
                                        Create A Public Good Game With Parameters
                                    </legend>
                                    <label for=\"gameName\">Game Name:* </label>
                                    <input type=\"text\" id=\"gameName\" name=\"gameName\">
                                    <span class=\"errorFeedback errorSpan\" id=\"gameNameError\">Game Name is required</span>
                                    <br />
                                    <label for=\"walletSize\">Wallet Size:* </label>
                                    <input type=\"text\" id=\"walletSize\" name=\"walletSize\">
                                    <span class=\"errorFeedback errorSpan\" id=\"walletSizeError\">Wallet size is required</span>
                                    <br />
                                    <label for=\"multiplier\">Multiplier:* </label>
                                    <input type=\"text\" id=\"multiplier\" name=\"multiplier\">
                                    <span class=\"errorFeedback errorSpan\" id=\"multiplierError\">Multiplier required</span>
                                    <br />
                                    <input type=\"submit\" id=\"submit\" name=\"submit\">
                                </fieldset>
                            </div>
                        </form>";
            }
        ?>
    </div>

</div>
<div>
    <a href="logout.php">Click here to logout</a>
</div>
</body>
</html>

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

</body>
</html>
