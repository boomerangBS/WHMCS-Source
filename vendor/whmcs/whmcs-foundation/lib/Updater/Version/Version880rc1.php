<?php

namespace WHMCS\Updater\Version;

class Version880rc1 extends IncrementalVersion
{
    protected $updateActions = ["removeHeartInternetModules", "addConfigurationFileDatabaseTLsDefaultsIfMissing", "updateGocardlessTypeSetting"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "/configuration.php.new";
        $whatsNewPath = ROOTDIR . "/admin/images/whatsnew/";
        $this->filesToRemove[] = $whatsNewPath . "icon-360monitoring.png";
        $this->filesToRemove[] = $whatsNewPath . "icon-centralnic.png";
        $this->filesToRemove[] = $whatsNewPath . "icon-instantissuance.png";
        $this->filesToRemove[] = $whatsNewPath . "icon-nord.png";
    }
    public function removeHeartInternetModules() : \self
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused([\WHMCS\Module\AbstractModule::TYPE_REGISTRAR => ["heartinternet"], \WHMCS\Module\AbstractModule::TYPE_SERVER => ["heartinternet"]]);
        return $this;
    }
    public function addConfigurationFileDatabaseTlsDefaultsIfMissing()
    {
        $configKeys = ["db_tls_ca" => "", "db_tls_ca_path" => "", "db_tls_cert" => "", "db_tls_cipher" => "", "db_tls_key" => "", "db_tls_verify_cert" => ""];
        try {
            $configFile = ROOTDIR . "/" . \WHMCS\Config\Application::WHMCS_DEFAULT_CONFIG_FILE;
            $applicationWriter = new \WHMCS\Config\ApplicationWriter();
            $currentConfig = (new \WHMCS\Config\Application())->loadConfigFile($configFile);
            foreach ($configKeys as $name => $unused) {
                if(is_null($currentConfig->offsetGet($name))) {
                    $applicationWriter->setValue($name, "");
                }
            }
        } catch (\Throwable $e) {
            $msg = sprintf("Updater unable to add default database tls options to configuration file: %s", $e->getMessage());
            logActivity($msg);
        }
    }
    public function getFeatureHighlights()
    {
        return [new \WHMCS\Notification\FeatureHighlight("On-Demand Renewals", "Let clients renew their services in the Client Area before invoicing occurs.", NULL, "icon-ondemandrenewals.png", "Help your customers avoid missed due dates easily.", "https://go.whmcs.com/1785/on-demand-renewals", "Learn More"), new \WHMCS\Notification\FeatureHighlight("Encrypted MySQL&reg; Connection Support", "WHMCS now supports encrypted connections to MySQL databases.", NULL, "icon-mysql.png", "Protect your business's valuable data.", "https://go.whmcs.com/1781/encrypted-mysql", "Learn More"), new \WHMCS\Notification\FeatureHighlight("WP Squared", "Provision accounts and sell hosting on WP Squared servers.", NULL, "icon-wp2.png", "Take advantage of a tailored WordPress hosting server.", "https://go.whmcs.com/1793/wp-squared", "Learn More"), new \WHMCS\Notification\FeatureHighlight("One-Click Stripe Payments", "Link lets your customers make secure one-click payments.", NULL, "icon-stripelink.png", "Link is available for all Stripe transactions.", "https://go.whmcs.com/1789/stripe-link", "Learn More")];
    }
    public function updateGocardlessTypeSetting()
    {
        \WHMCS\Database\Capsule::table("tblpaymentgateways")->where("gateway", "gocardless")->where("setting", "type")->update(["value" => \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD]);
        return $this;
    }
}

?>