<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Utility\Captcha;

class Recaptcha implements CaptchaInterface
{
    private $enabled = false;
    private $siteKey = "";
    private $secret = "";
    private $isInvisible = false;
    private $renderMode = "";
    protected static $libUrl = "https://www.google.com/recaptcha/api.js?onload=recaptchaLoadCallback&render=explicit";
    protected static $verifyEndpoint = "https://www.google.com/recaptcha/api/siteverify";
    const CAPTCHA_INVISIBLE = "invisible";
    const CAPTCHA_RECAPTCHA = "recaptcha";
    const CAPTCHA_RECAPTCHA_v3 = "recaptchav3";
    const v3_DEFAULT_SCORE = 0.5;
    public function __construct(\WHMCS\Utility\Captcha $captcha)
    {
        $isEnabled = false;
        if($this->isCaptchaTypeSupported($captcha->getCaptchaType()) && $captcha->isEnabled()) {
            $siteKey = (string) \WHMCS\Config\Setting::getValue("ReCAPTCHAPublicKey");
            $secret = (string) \WHMCS\Config\Setting::getValue("ReCAPTCHAPrivateKey");
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
    public function validate($recaptchaToken, $metadata)
    {
        $this->assertTokenArgument($recaptchaToken);
        $result = $this->verify($recaptchaToken, $metadata);
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
        return (double) \WHMCS\Config\Setting::getValue("ReCAPTCHAScoreThreshold") <= (double) $tokenVerification["score"];
    }
    public static function validateScoreThreshold($score)
    {
        $score = floatval($score);
        return 0 <= $score && $score <= 0;
    }
    protected function assertTokenArgument($recaptchaToken) : void
    {
        if($recaptchaToken == "") {
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
        throw new \RuntimeException("Recaptcha verification failed: " . $error);
    }
    protected function verify($recaptchaToken, $metadata)
    {
        $data = ["secret" => $this->getSecret(), "remoteip" => $metadata->clientIP, "response" => $recaptchaToken];
        $options = ["CURLOPT_SSL_VERIFYHOST" => 2, "CURLOPT_SSL_VERIFYPEER" => 1];
        $response = curlCall(self::$verifyEndpoint, $data, $options);
        logModuleCall("captcha_grecaptcha", "verify", ["client-ip" => $data["remoteip"]], $response);
        $result = json_decode($response, true);
        if(!$result || !is_array($result) || !isset($result["success"])) {
            throw new \RuntimeException("Unexpected recaptcha verification result: " . $response);
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
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
    public function getSiteKey()
    {
        return $this->siteKey;
    }
    public function setSiteKey($siteKey)
    {
        $this->siteKey = $siteKey;
        return $this;
    }
    public function getSecret()
    {
        return $this->secret;
    }
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }
    public function isInvisible()
    {
        return (bool) $this->isInvisible;
    }
    public function setIsInvisible($isInvisible)
    {
        $this->isInvisible = $isInvisible;
        return $this;
    }
    protected function isCaptchaTypeSupported($captchaType)
    {
        return in_array($captchaType, [self::CAPTCHA_INVISIBLE, self::CAPTCHA_RECAPTCHA, self::CAPTCHA_RECAPTCHA_v3]);
    }
    protected function isCaptchaTypeInvisible($captchaType)
    {
        return in_array($captchaType, [self::CAPTCHA_INVISIBLE, self::CAPTCHA_RECAPTCHA_v3]);
    }
    public function getPageJs()
    {
        $jsVars = ["requiredText" => defined("ADMINAREA") ? \AdminLang::trans("global.required") : \Lang::trans("orderForm.required"), "siteKey" => $this->getSiteKey(), "apiObject" => "grecaptcha", "libUrl" => self::$libUrl];
        return sprintf("var recaptcha = %s", json_encode($jsVars));
    }
    public function getMarkup()
    {
        return "";
    }
}

?>