<?php

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