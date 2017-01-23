<?php
/**
 * Created by PhpStorm.
 * User: richardpoutier
 * Date: 1/2/17
 * Time: 6:37 PM
 */

require_once ('functions.inc');
$user = new User;
$user->logout();
die(header("Location: login.php"));

?>