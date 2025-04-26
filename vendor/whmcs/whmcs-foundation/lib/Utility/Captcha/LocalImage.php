<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Utility\Captcha;

class LocalImage implements CaptchaInterface
{
    public function isEnabled()
    {
        return false;
    }
    public function renderMode()
    {
        return CaptchaInterface::RENDER_MODE_INPUT;
    }
    public function validate($token, $metadata)
    {
        $expectedValue = \WHMCS\Session::getAndDelete("captchaValue");
        generateNewCaptchaCode();
        return $expectedValue == md5(strtoupper($token));
    }
    public function getPageJs()
    {
        return "";
    }
    public function getMarkup()
    {
        return "";
    }
    public function isInvisible()
    {
        return false;
    }
}

?>