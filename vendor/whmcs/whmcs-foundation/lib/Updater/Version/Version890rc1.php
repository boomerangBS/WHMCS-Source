<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version890rc1 extends IncrementalVersion
{
    protected $updateActions = ["addViewInvoicePermissionToAppropriateRoles", "addStripeACHToGatewayHooksIfActive"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "/.gitlab-ci.yml";
        $this->filesToRemove[] = ROOTDIR . "/modules/security/duosecurity/Duo-Web-v2.min.js";
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/stripe_ach/lib/Exception/";
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/stripe_ach/lib/Plaid.php";
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/stripe_ach/lib/StripeAchController.php";
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/stripe_ach/lib/StripeAchRouteProvider.php";
    }
    public function addViewInvoicePermissionToAppropriateRoles() : \self
    {
        $viewInvoicePermissionId = \WHMCS\User\Admin\Permission::findId("View Invoice");
        $manageInvoicePermissionId = \WHMCS\User\Admin\Permission::findId("Manage Invoice");
        $existingRolesWithManageInvoices = \WHMCS\Database\Capsule::table("tbladminperms")->where("permid", $manageInvoicePermissionId)->pluck("roleid")->toArray();
        $existingRolesWithViewInvoices = \WHMCS\Database\Capsule::table("tbladminperms")->where("permid", $viewInvoicePermissionId)->pluck("roleid")->toArray();
        if($existingRolesWithManageInvoices) {
            $newValues = [];
            foreach ($existingRolesWithManageInvoices as $existingRolesWithManageInvoice) {
                if(in_array($existingRolesWithManageInvoice, $existingRolesWithViewInvoices)) {
                } else {
                    $newValues[] = ["roleid" => $existingRolesWithManageInvoice, "permid" => $viewInvoicePermissionId];
                }
            }
            if(count($newValues)) {
                \WHMCS\Database\Capsule::table("tbladminperms")->insert($newValues);
            }
        }
        return $this;
    }
    public function addStripeACHToGatewayHooksIfActive() : \self
    {
        $module = "stripe_ach";
        $activeGateways = \WHMCS\Module\GatewaySetting::getActiveGatewayModules();
        if(in_array($module, $activeGateways)) {
            $currentGatewayHooks = array_filter(explode(",", \WHMCS\Config\Setting::getValue("GatewayModuleHooks")));
            if(!in_array($module, $currentGatewayHooks)) {
                $currentGatewayHooks[] = $module;
                \WHMCS\Config\Setting::setValue("GatewayModuleHooks", implode(",", $currentGatewayHooks));
            }
        }
        return $this;
    }
    public function getFeatureHighlights()
    {
        return [(new \WHMCS\Notification\FeatureHighlight("PayPal Payments with PayPal&reg; Vault", "Accept payments using PayPal Smart Buttons and PayPal's latest secure tokenization system.", NULL, "icon-paypal.png", "Our newest payment gateway option!", "https://go.whmcs.com/1797/PayPal", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("PayPal&reg; Card Payments", "Accept credit card payments through PayPal&reg; using PayPal Advanced Cards.", NULL, "icon-pp-cards.png", "Start taking payments with PayPal Advanced Cards today!", "https://go.whmcs.com/1797/PayPal", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("Separate Invoice Viewing and Management", "Admin Area invoices now default to a view-only mode with separate permissions for viewing and management.", NULL, "icon-admin-view.png", "Tailor permissions to each staff member's needs and prevent accidental changes.", "https://go.whmcs.com/1817/Invoice_Management", "Learn More"))->hideIconBackgroundImage()];
    }
}

?>