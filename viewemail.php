<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("CLIENTAREA", true);
require "init.php";
$whmcs = App::self();
$emailId = (int) $whmcs->get_req_var("id");
$ca = new WHMCS\ClientArea();
$ca->setPageTitle(Lang::trans("clientareaemails"));
$ca->addToBreadCrumb("index.php", $whmcs->get_lang("globalsystemname"));
$ca->addToBreadCrumb("viewemail.php?id=" . (int) $emailId . "#", Lang::trans("clientareaemails"));
$ca->initPage();
$ca->requireLogin();
checkContactPermission("emails");
$data = WHMCS\Mail\Log::ofClient(Auth::client()->id)->where("id", $emailId)->first();
if(!$data) {
    exit("Invalid Access Attempt");
}
$date = $data->getRawAttribute("date");
$subject = $data->subject;
$message = $data->message;
$attachments = $data->attachments;
$date = fromMySQLDate($date, true, true);
$ca->assign("date", WHMCS\Input\Sanitize::makeSafeForOutput($date));
$ca->assign("subject", WHMCS\Input\Sanitize::makeSafeForOutput($subject));
$message = WHMCS\Input\Sanitize::maskEmailVerificationId($message);
$ca->assign("message", $message);
$ca->assign("attachments", $attachments);
$ca->setTemplate("viewemail");
$ca->disableHeaderFooterOutput();
$ca->addOutputHookFunction("ClientAreaPageViewEmail");
$ca->output();

?>