<?php


namespace WHMCS\Utility;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F5574696C6974792F436170746368612E7068703078376664353934323439393661_
{
    public $clientIP;
}
class Captcha
{
    private $enabled = false;
    private $forms = [];
    public $recaptcha;
    private $captchaType = "";
    public static $defaultFormSettings;
    const SETTING_CAPTCHA_FORMS = "CaptchaForms";
    const FORM_CHECKOUT_COMPLETION = "checkoutCompletion";
    const FORM_DOMAIN_CHECKER = "domainChecker";
    const FORM_REGISTRATION = "registration";
    const FORM_CONTACT_US = "contactUs";
    const FORM_SUBMIT_TICKET = "submitTicket";
    const FORM_LOGIN = "login";
    public function __construct()
    {
        $isEnabled = $this->isSystemEnabledRuntime();
        $this->captchaType = \WHMCS\Config\Setting::getValue("CaptchaType");
        $this->setEnabled($isEnabled);
        $storedForms = $this->getStoredFormSettings();
        $defaultForms = static::getDefaultFormSettings();
        $this->setForms(array_merge($defaultForms, $storedForms));
        $this->recaptcha = self::factoryHandler($this->captchaType);
        if($this->captchaType != "" && !$this->recaptcha->isEnabled()) {
            $this->recaptcha = new Captcha\LocalImage();
            $this->captchaType = "";
        }
    }
    protected function factoryHandler($captchaType) : Captcha\CaptchaInterface
    {
        if(in_array($captchaType, [Captcha\Recaptcha::CAPTCHA_RECAPTCHA, Captcha\Recaptcha::CAPTCHA_INVISIBLE, Captcha\Recaptcha::CAPTCHA_RECAPTCHA_v3])) {
            return new Captcha\Recaptcha($this);
        }
        if(in_array($captchaType, [Captcha\HCaptcha::HCAPTCHA_VISIBLE, Captcha\HCaptcha::HCAPTCHA_INVISIBLE])) {
            return new Captcha\HCaptcha($this);
        }
        if($captchaType == "") {
            return new Captcha\LocalImage();
        }
        throw new \RuntimeException("Unknown captcha handler '" . $captchaType . "'");
    }
    public function isSystemEnabledRuntime()
    {
        $setting = trim((string) \WHMCS\Config\Setting::getValue("CaptchaSetting"));
        if($setting == "on") {
            return true;
        }
        $clientAreaLoggedIn = defined("CLIENTAREA") && \Auth::user();
        $adminAreaLoggedIn = defined("ADMINAREA") && \WHMCS\Session::get("adminid");
        $clientAreaLoggedIn or $isLoggedIn = $clientAreaLoggedIn || $adminAreaLoggedIn;
        if(!$setting || $setting && $isLoggedIn) {
            return false;
        }
        return true;
    }
    public static function getDefaultFormSettings()
    {
        return static::$defaultFormSettings;
    }
    public function validateAppropriateCaptcha($form, \WHMCS\Validate $validate)
    {
        if($this->isEnabled() && $this->isEnabledForForm($form)) {
            if($this->recaptcha->isEnabled()) {
                try {
                    if(!$this->recaptcha->validate((string) \App::getFromRequest("g-recaptcha-response"), $this->metadataValidate())) {
                        $message = defined("ADMINAREA") ? \AdminLang::trans("captcha.verification.failed") : \Lang::trans("captcha.verification.failed");
                        throw new \RuntimeException($message);
                    }
                } catch (\Exception $e) {
                    if($e->getMessage() === "captchaIncorrect") {
                        $languageKey = "captchaIncorrect";
                        if(defined("ADMINAREA")) {
                            $validate->addError("Please complete the captcha and try again.");
                        } else {
                            $validate->addError($languageKey);
                        }
                    } else {
                        $validate->addError($e->getMessage());
                    }
                    return false;
                }
            } else {
                if(!$this->recaptcha->validate((string) \App::getFromRequest("code"), $this->metadataValidate())) {
                    $validate->addError(defined("ADMINAREA") ? \AdminLang::trans("captchaverifyincorrect") : "captchaverifyincorrect");
                    return false;
                }
                return true;
            }
        }
        return true;
    }
    public function metadataValidate()
    {
        $o = new func_num_args();
        $o->clientIP = Environment\CurrentRequest::getIP();
        return $o;
    }
    public function getForms()
    {
        return $this->forms;
    }
    public function setForms($forms)
    {
        $this->forms = $forms;
        return $this;
    }
    public function isEnabledForForm($formName)
    {
        if($this->isEnabled()) {
            $forms = $this->getForms();
            if(!array_key_exists($formName, $forms)) {
                return true;
            }
            return (bool) $forms[$formName];
        }
        return false;
    }
    public function getStoredFormSettings()
    {
        $data = \WHMCS\Config\Setting::getValue(static::SETTING_CAPTCHA_FORMS);
        if(!is_string($data) || strlen($data) == 0) {
            return [];
        }
        $data = json_decode($data, true);
        if(!is_array($data)) {
            $data = [];
        }
        return $data;
    }
    public function setStoredFormSettings(array $data = [])
    {
        \WHMCS\Config\Setting::setValue(static::SETTING_CAPTCHA_FORMS, json_encode($data));
        return $this;
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
    public function __toString()
    {
        return $this->getCaptchaType();
    }
    public function getCaptchaType()
    {
        return $this->captchaType ?: "";
    }
    public function getButtonClass($formName)
    {
        $classArr = [];
        $class = "";
        if($this->isEnabledForForm($formName)) {
            if($this->recaptcha->isEnabled()) {
                $classArr[] = "btn-recaptcha";
            }
            if($this->recaptcha->renderMode() == Captcha\CaptchaInterface::RENDER_MODE_HIDDEN) {
                $classArr[] = "btn-recaptcha-invisible";
            }
            if(!empty($classArr)) {
                $class .= " " . implode(" ", $classArr);
            }
        }
        return $class;
    }
    public function getPageJs()
    {
        if($this->recaptcha->isEnabled()) {
            return $this->recaptcha->getPageJs();
        }
        return "";
    }
    public function getMarkup()
    {
        if($this->recaptcha->isEnabled()) {
            return $this->recaptcha->getMarkup();
        }
        return "";
    }
    public static function overlayBadge($imgSrc = "", string $imgAlt = NULL, string $popupText = false, $hidden)
    {
        $popup = !is_null($popupText) ? "<div class=\"captcha-overlay-popup\">" . $popupText . "</div>" : "";
        $classHidden = $hidden ? " captcha-overlay-badge-hidden" : "";
        return "        <div class=\"captcha-overlay-badge" . $classHidden . "\">\n            <img src=\"" . $imgSrc . "\" alt=\"" . $imgAlt . "\">\n        </div>\n        " . $popup;
    }
}

?>