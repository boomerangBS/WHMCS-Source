<?php

if(!defined("WHMCS")) {
    header("Location: clientarea.php");
    exit;
}
Auth::requireLoginAndClient(true);

?>