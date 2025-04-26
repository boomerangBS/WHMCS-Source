<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Utility\Captcha;

interface CaptchaInterface
{
    const RENDER_MODE_CHECKBOX = "checkbox";
    const RENDER_MODE_HIDDEN = "invisible";
    const RENDER_MODE_INPUT = "input";
    public function isEnabled();
    public function renderMode();
    public function validate($token, $metadata);
    public function getPageJs();
    public function getMarkup();
}

?>