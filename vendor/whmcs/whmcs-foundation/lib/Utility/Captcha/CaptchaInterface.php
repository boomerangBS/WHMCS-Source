<?php

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