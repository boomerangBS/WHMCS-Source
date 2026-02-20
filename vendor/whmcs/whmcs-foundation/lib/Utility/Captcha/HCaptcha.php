<?php

namespace WHMCS\Utility\Captcha;

class HCaptcha implements CaptchaInterface
{
    private $enabled = false;
    private $siteKey = "";
    private $secret = "";
    private $isInvisible = false;
    private $renderMode = "";
    protected static $libUrl = "https://js.hcaptcha.com/1/api.js?onload=recaptchaLoadCallback&render=explicit";
    protected static $verifyEndpoint = "https://api.hcaptcha.com/siteverify";
    const HCAPTCHA_VISIBLE = "hcaptcha";
    const HCAPTCHA_INVISIBLE = "hcaptcha-invisible";
    const DEFAULT_SCORE = 0.5;
    public function __construct(\WHMCS\Utility\Captcha $captcha)
    {
        $isEnabled = false;
        if($this->isCaptchaTypeSupported($captcha->getCaptchaType()) && $captcha->isEnabled()) {
            $siteKey = (string) \WHMCS\Config\Setting::getValue("hCaptchaPublicKey");
            $secret = (string) \WHMCS\Config\Setting::getValue("hCaptchaPrivateKey");
            if($siteKey && $secret) {
                $this->setSiteKey($siteKey)->setSecret($secret)->setIsInvisible($this->isCaptchaTypeInvisible($captcha->getCaptchaType()));
                $isEnabled = true;
            }
            $this->setRenderMode($captcha->getCaptchaType());
        }
        $this->setEnabled($isEnabled);
    }
    protected function setRenderMode($captchaType) : \self
    {
        $this->renderMode = $this->isInvisible() ? CaptchaInterface::RENDER_MODE_HIDDEN : CaptchaInterface::RENDER_MODE_CHECKBOX;
        return $this;
    }
    public function renderMode()
    {
        return $this->renderMode;
    }
    public function validate($captchaToken, $metadata)
    {
        $this->assertTokenArgument($captchaToken);
        $result = $this->verify($captchaToken, $metadata);
        $this->assertNoError($result["error-codes"] ?? NULL);
        $verified = $this->inspectVerificationSuccess($result) ?? true;
        $verified = $verified && ($this->inspectVerificationMeetsScoreThreshold($result) ?? true);
        return $verified;
    }
    protected function inspectVerificationSuccess($tokenVerification) : array
    {
        return $tokenVerification["success"];
    }
    protected function inspectVerificationMeetsScoreThreshold($tokenVerification) : array
    {
        if(!isset($tokenVerification["score"])) {
            return NULL;
        }
        return (double) $tokenVerification["score"] <= (double) \WHMCS\Config\Setting::getValue("hCaptchaScoreThreshold");
    }
    protected function assertTokenArgument($captchaToken) : void
    {
        if($captchaToken == "") {
            throw new \InvalidArgumentException("captchaIncorrect");
        }
    }
    protected function assertNoError($errorCodes) : void
    {
        $error = "Unknown error";
        if(is_null($errorCodes)) {
            return NULL;
        }
        if(is_array($errorCodes)) {
            $error = implode(",", $errorCodes);
        } elseif(is_string($errorCodes) && 0 < strlen($errorCodes)) {
            $error = $errorCodes;
        }
        throw new \RuntimeException("hCaptcha verification failed: " . $error);
    }
    protected function verify($captchaToken, $metadata) : array
    {
        $data = ["secret" => $this->getSecret(), "remoteip" => $metadata->clientIP, "response" => $captchaToken];
        $options = ["CURLOPT_SSL_VERIFYHOST" => 2, "CURLOPT_SSL_VERIFYPEER" => 1];
        $response = curlCall(self::$verifyEndpoint, $data, $options);
        logModuleCall("captcha_hcaptcha", "verify", ["client-ip" => $data["remoteip"]], $response);
        $result = json_decode($response, true);
        if(!is_array($result) || !isset($result["success"])) {
            throw new \RuntimeException("Unexpected hcaptcha verification result: " . $response);
        }
        if(!is_bool($result["success"])) {
            $result["success"] = $result["success"] === "true";
        }
        return $result;
    }
    public function isEnabled()
    {
        return $this->enabled;
    }
    public function setEnabled($enabled) : \self
    {
        $this->enabled = $enabled;
        return $this;
    }
    public function getSiteKey()
    {
        return $this->siteKey;
    }
    public function setSiteKey($siteKey) : \self
    {
        $this->siteKey = $siteKey;
        return $this;
    }
    public function getSecret()
    {
        return $this->secret;
    }
    public function setSecret($secret) : \self
    {
        $this->secret = $secret;
        return $this;
    }
    public function isInvisible()
    {
        return (bool) $this->isInvisible;
    }
    public function setIsInvisible($isInvisible) : \self
    {
        $this->isInvisible = $isInvisible;
        return $this;
    }
    protected function isCaptchaTypeSupported($captchaType)
    {
        return in_array($captchaType, [self::HCAPTCHA_VISIBLE, self::HCAPTCHA_INVISIBLE]);
    }
    protected function isCaptchaTypeInvisible($captchaType)
    {
        return in_array($captchaType, [self::HCAPTCHA_INVISIBLE]);
    }
    public function getPageJs()
    {
        $jsVars = ["requiredText" => defined("ADMINAREA") ? \AdminLang::trans("global.required") : \Lang::trans("orderForm.required"), "apiObject" => "hcaptcha", "siteKey" => $this->getSiteKey(), "libUrl" => self::$libUrl];
        return sprintf("var recaptcha = %s;", json_encode($jsVars));
    }
    public function getMarkup()
    {
        if(!$this->isInvisible()) {
            return "";
        }
        $disclaimerUrls = [":privacyUrl" => "https://www.hcaptcha.com/privacy", ":termsUrl" => "https://www.hcaptcha.com/terms"];
        $disclaimer = defined("ADMINAREA") ? \AdminLang::trans("captcha.hcaptcha.disclaimer", $disclaimerUrls) : \Lang::trans("captcha.hcaptcha.disclaimer", $disclaimerUrls);
        unset($disclaimerUrls);
        $tagLine = defined("ADMINAREA") ? \AdminLang::trans("captcha.hcaptcha.tagLIne") : \Lang::trans("captcha.hcaptcha.tagLIne");
        return \WHMCS\Utility\Captcha::overlayBadge(\DI::make("asset")->getImgPath() . "/hcaptcha-logo.svg", $tagLine, $disclaimer, true);
    }
}

?>