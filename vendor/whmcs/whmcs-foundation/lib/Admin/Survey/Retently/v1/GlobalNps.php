<?php

namespace WHMCS\Admin\Survey\Retently\v1;

class GlobalNps
{
    protected $campaignId = "65abd5efe86c3212b888c1f5";
    protected $variant = "";
    const RETENTLY_ACCOUNT_ID = "606c61a4c8c2d36d01246bc4";
    const IDENTIFIER_TAG_VALUE = "whmcs-nps-v1";
    const TEST_VARIANTS = ["a", "b"];
    const WHITELISTED_PERMISSIONS = ["Main Homepage"];
    const BLACKLISTED_LICENSE_TIERS = ["Internal License", "Development License"];
    public function __construct(string $variant)
    {
        $this->variant = $variant;
    }
    protected static function isVariantValid($variant)
    {
        return in_array($variant, static::TEST_VARIANTS);
    }
    public static function hasVariants()
    {
        return static::TEST_VARIANTS;
    }
    public static function fromSetting()
    {
        return new static(static::getPersistedVariant() ?? "");
    }
    public static function getPersistedVariant()
    {
        return \WHMCS\Config\Setting::getValue(static::systemSetting());
    }
    public static function persistVariant($variant) : void
    {
        \WHMCS\Config\Setting::setValue(static::systemSetting(), $variant);
    }
    public static function unpersistVariant($variant) : void
    {
        \WHMCS\Config\Setting::deleteValue(static::systemSetting());
    }
    public static function ensureSettingVariant()
    {
        if(!static::hasVariants()) {
            return false;
        }
        $variant = static::getPersistedVariant();
        if(is_null($variant) || !static::isVariantValid($variant)) {
            static::persistVariant(static::selectTestVariant());
            return true;
        }
        return false;
    }
    public static function selectTestVariant()
    {
        if(!static::TEST_VARIANTS) {
            throw new \UnderflowException("No survey variants defined");
        }
        return static::TEST_VARIANTS[array_rand(static::TEST_VARIANTS)];
    }
    public static function systemSetting()
    {
        return "SurveyABTestVariation-" . static::IDENTIFIER_TAG_VALUE;
    }
    public static function shouldRender(string $currentPagePermission)
    {
        if(static::isLicenseTierBlacklisted() && !static::isSurveyAllowOverride()) {
            return false;
        }
        return in_array($currentPagePermission, static::WHITELISTED_PERMISSIONS);
    }
    protected static function isLicenseTierBlacklisted()
    {
        $license = \DI::make("license");
        return in_array($license->getProductName(), static::BLACKLISTED_LICENSE_TIERS);
    }
    protected static function isSurveyAllowOverride()
    {
        return \WHMCS\Config\Setting::getValue("AdminSurveyAllowOverride");
    }
    protected function getInstallIdentifier()
    {
        return sha1(\App::getWHMCSInstanceID());
    }
    protected function getABTestVariation()
    {
        return $this->variant;
    }
    protected function getInstalledVersion()
    {
        return implode(".", [\App::getVersion()->getMajor(), \App::getVersion()->getMinor()]);
    }
    protected function getLicenseTier()
    {
        $license = \DI::make("license");
        $licenseTier = $license->getProductName();
        return $licenseTier ? $licenseTier : "Unknown";
    }
    protected function getLicenseStartDate()
    {
        $license = \DI::make("license");
        $regDate = $license->getRegistrationDate();
        return $regDate ? $regDate : "Unknown";
    }
    protected function getLicenseDaysSinceCreation()
    {
        $license = \DI::make("license");
        $regDate = $license->getRegistrationDate();
        try {
            return \WHMCS\Carbon::parse($regDate)->diffInDays(\WHMCS\Carbon::now());
        } catch (\Exception $e) {
            return 0;
        }
    }
    protected function getSanitizedAdminRole(\WHMCS\User\Admin $admin)
    {
        return preg_replace("/[^a-z0-9]/", "", strtolower($admin->getRoleName()));
    }
    protected function getTags()
    {
        $tags = [static::IDENTIFIER_TAG_VALUE];
        $variant = $this->getABTestVariation();
        if($variant != "") {
            $tags[] = ltrim(sprintf("%s-%s", static::IDENTIFIER_TAG_VALUE, $this->getABTestVariation()), "-");
        }
        return array_filter($tags);
    }
    protected function getAdminEmailHash()
    {
        return sha1($admin->email) . "." . substr(sha1($this->campaignId), 0, 10) . "@" . substr($this->getInstallIdentifier(), 0, 10) . ".whmcs.user";
    }
    protected function e($val)
    {
        return str_replace("\"", "", $val);
    }
    public function generateOutput(\WHMCS\User\Admin $admin)
    {
        return "<div\n    id=\"retently-survey-embed\"\n    data-href=\"https://app.retently.com/api/remote/tracking/" . static::RETENTLY_ACCOUNT_ID . "\"\n    data-rel=\"dialog\"\n    data-email=\"" . $this->getAdminEmailHash() . "\"\n    data-company=\"" . $this->e(\WHMCS\Config\Setting::getValue("CompanyName")) . "\"\n    data-prop-whmcs_version=\"" . $this->getInstalledVersion() . "\"\n    data-prop-whmcs_version_tier=\"" . \App::getVersion()->getPreReleaseIdentifier() . "\"\n    data-prop-whmcs_user_language=\"" . $this->e($admin->language) . "\"\n    data-prop-whmcs_user_role=\"" . $this->getSanitizedAdminRole($admin) . "\"\n    data-prop-whmcs_install_id=\"" . $this->getInstallIdentifier() . "\"\n    data-prop-whmcs_license_tier=\"" . $this->getLicenseTier() . "\"\n    data-prop-whmcs_license_start=\"" . $this->getLicenseStartDate() . "\"\n    data-prop-days-since-creation=\"" . $this->getLicenseDaysSinceCreation() . "\"\n    data-tags=\"" . implode(",", $this->getTags($admin)) . "\"\n    data-campaignId=\"" . $this->campaignId . "\"\n></div>\n<script type='text/javascript'>\n (function (d, s, id) {\n      var js, rjs = d.getElementsByTagName(s)[0];\n      if (d.getElementById(id)) return;\n      js = d.createElement(s);\n      js.id = id;\n      js.src = \"https://cdn.retently.com/public/components/embed/sdk.min.js\";\n      rjs.parentNode.insertBefore(js, rjs);\n }(document, 'script', 'retently-jssdk'));\n</script>";
    }
}

?>