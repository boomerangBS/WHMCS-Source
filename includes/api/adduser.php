<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$firstname = App::getFromRequest("firstname");
$lastname = App::getFromRequest("lastname");
$email = App::getFromRequest("email");
$password2 = App::getFromRequest("password2");
$language = App::getFromRequest("language");
if(!$firstname) {
    $apiresults = ["result" => "error", "message" => "You did not enter a first name"];
} elseif(!$lastname) {
    $apiresults = ["result" => "error", "message" => "You did not enter a last name"];
} elseif(!$email) {
    $apiresults = ["result" => "error", "message" => "You did not enter an email address"];
} elseif(!$password2) {
    $apiresults = ["result" => "error", "message" => "You did not enter a password"];
} elseif(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $apiresults = ["result" => "error", "message" => "The email address entered is not valid"];
} else {
    try {
        $user = WHMCS\User\User::createUser($firstname, $lastname, $email, WHMCS\Input\Sanitize::decode($password2), $language);
    } catch (WHMCS\Exception\User\EmailAlreadyExists $e) {
        $apiresults = ["result" => "error", "message" => "A user already exists with that email address"];
        return NULL;
    } catch (Exception $e) {
        $apiresults = ["result" => "error", "message" => $e->getMessage()];
        return NULL;
    }
    $apiresults = ["result" => "success", "user_id" => $user->id];
}

?>