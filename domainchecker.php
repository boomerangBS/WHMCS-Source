<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "init.php";
require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "clientareafunctions.php";
$domain = WHMCS\Input\Sanitize::decode(App::getFromRequest("domain"));
$ext = App::getFromRequest("ext");
$sld = App::getFromRequest("sld");
$tld = App::getFromRequest("tld");
$tlds = App::getFromRequest("search_tlds");
$captcha = new WHMCS\Utility\Captcha();
$validate = new WHMCS\Validate();
$captcha->validateAppropriateCaptcha(WHMCS\Utility\Captcha::FORM_DOMAIN_CHECKER, $validate);
if($validate->hasErrors()) {
    WHMCS\Session::set("captchaData", ["invalidCaptcha" => true, "invalidCaptchaError" => $validate->getErrors()[0]]);
    WHMCS\Session::set("CaptchaComplete", false);
} else {
    WHMCS\Session::set("captchaData", ["invalidCaptcha" => false, "invalidCaptchaError" => false]);
    WHMCS\Session::set("CaptchaComplete", true);
}
if(in_array($domain, [Lang::trans("domaincheckerdomainexample")])) {
    $domain = "";
}
if($ext && $domain) {
    if(substr($ext, 0, 1) != ".") {
        $ext = "." . $ext;
    }
    $domain .= $ext;
}
if(!$domain && $sld && $tld) {
    if(substr($tld, 0, 1) != ".") {
        $tld = "." . $tld;
    }
    $domain = $sld . $tld;
}
if(is_array($tlds) && 0 < count($tlds)) {
    $tldToAppend = $tlds[0];
    if(substr($tldToAppend, 0, 1) != ".") {
        $tldToAppend = "." . $tldToAppend;
    }
    if($domain) {
        $domain = $domain . $tldToAppend;
    } elseif($sld) {
        $domain = $sld . $tldToAppend;
    }
}
$domainRequestSuffix = $domain ? "&query=" . urlencode($domain) : "";
if(App::getFromRequest("transfer")) {
    App::redirect("cart.php", "a=add&domain=transfer" . $domainRequestSuffix);
}
if(App::getFromRequest("hosting")) {
    App::redirect("cart.php", substr($domainRequestSuffix, 1));
}
App::redirect("cart.php", "a=add&domain=register" . $domainRequestSuffix);

?>