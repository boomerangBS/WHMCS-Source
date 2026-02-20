<?php

namespace WHMCS\Updater\Version;

class Version8110rc1 extends IncrementalVersion
{
    protected $updateActions = ["updateSitejetPromotionDismissalKey"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove = ["//modules/gateways/paypal_acdc/lib/ModuleFunctionResult/Capture.php", "//modules/gateways/paypal_ppcpv/lib/API/KnownCustomerCreateOrderRequest.php", "//modules/gateways/paypal_ppcpv/lib/API/NewCustomerCreateOrderRequest.php", "//includes/recaptchalib.php", "//vendor/whmcs/whmcs-foundation/lib/Admin/Utilities/Sitejet/SitejetPromptController.php", "//vendor/whmcs/whmcs-foundation/lib/Admin/Wizard/Steps/GettingStarted/CreditCard.php"];
    }
    public function updateSitejetPromotionDismissalKey()
    {
        \WHMCS\Database\Capsule::table("tbltransientdata")->where("name", "HideSitejetPrompt")->update(["name" => "HidePromoSitejet"]);
    }
}

?>