<?php

namespace WHMCS\Updater\Version;

class Version8100rc1 extends IncrementalVersion
{
    protected $updateActions = ["registerInvoiceAutoCancellationCronTask"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove = array_merge($this->filesToRemove, self::modulePayPalCommerceFiles());
    }
    public static function modulePayPalCommerceFiles() : array
    {
        return array_map(function ($f) {
            return ROOTDIR . "/modules/gateways/paypal_ppcpv" . $f;
        }, ["/lib/API/CustomerAccessTokenResponse.php", "/lib/API/KnownCustomerAccessTokenRequest.php", "/lib/API/NewCustomerAccessTokenRequest.php", "/lib/Handler/InvoicePaymentHandler.php", "/lib/Handler/Link.php", "/lib/Handler/ModuleHandlerInterface.php", "/lib/Handler/ReturnBuyer.php"]);
    }
    public function getFeatureHighlights()
    {
        return [(new \WHMCS\Notification\FeatureHighlight("Sitejet Builder for cPanel &amp; WHM and Plesk", "Offer an easy-to-use, seamlessly-integrated website builder for cPanel and Plesk — at no additional cost!", NULL, "icon-sitejet.png", "Check out why you should resell Sitejet Builder.", "https://go.whmcs.com/1829/Sitejet_Builder", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("Automatic Cancellation of Overdue Invoices", "Automation Settings now offers controls to cancel overdue invoices after a specified number of days.", NULL, "icon-auto-cancellation.png", "Automatically transition overdue invoices to increase your productivity and maintain accurate liability reporting.", "https://go.whmcs.com/1833/Automation_Settings", "Learn More"))->hideIconBackgroundImage()];
    }
    protected function registerInvoiceAutoCancellationCronTask()
    {
        \WHMCS\Cron\Task\InvoiceAutoCancellation::register();
        return $this;
    }
}

?>