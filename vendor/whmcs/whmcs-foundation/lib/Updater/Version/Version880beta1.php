<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version880beta1 extends IncrementalVersion
{
    protected $updateActions = ["removeBizCnModule", "removeLinkPointModule", "removeSlimPayModule", "updateQuoteAcceptedEmailTemplate", "updateCancellationRequestConfirmationEmailTemplate", "createOnDemandRenewalsTable", "updateOrderRenewalsFormat"];
    const QUOTE_ACCEPTED_EMAIL_TEMPLATE = "<p>Dear {\$client_name},</p><p>This is a confirmation that you have accepted the generated quote from {\$quote_date_created}.</p><p>{\$signature}</p>";
    const CANCELLATION_REQUEST_CONFIRMATION = "<p>Dear {\$client_name},</p><p>This email is to confirm that we have received your cancellation request for the service listed below.</p><p>Product/Service: {\$service_product_name}<br />Domain: {\$service_domain}</p><p>{if \$service_is_immediate_cancellation}The service will be terminated within the next 24 hours.{else}The service will be cancelled at the end of your current billing period on {\$service_next_due_date}.{/if}</p><p>Thank you for using {\$company_name} and we hope to see you again in the future.</p><p>{\$signature}</p>";
    public function removeBizCnModule() : \self
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused(["registrars" => ["bizcn"]]);
        return $this;
    }
    public function removeLinkPointModule() : \self
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused(["gateways" => ["linkpoint"]]);
        return $this;
    }
    public function removeSlimPayModule() : \self
    {
        (new \WHMCS\Module\LegacyModuleCleanup())->removeModulesIfInstalledAndUnused(["gateways" => ["slimpay"]]);
        return $this;
    }
    public function updateQuoteAcceptedEmailTemplate() : \self
    {
        $emailMD5Values = ["87e7703d9e5ea91498948ef8c666c4a1"];
        $templateTitle = "Quote Accepted";
        $template = \WHMCS\Mail\Template::master()->where("name", $templateTitle)->where("language", "")->first();
        if(in_array(md5($template->message), $emailMD5Values)) {
            $template->message = trim(self::QUOTE_ACCEPTED_EMAIL_TEMPLATE);
            $template->save();
            logActivity(sprintf("Email Template `%s` Updated", $templateTitle));
        }
        return $this;
    }
    public function updateCancellationRequestConfirmationEmailTemplate() : \self
    {
        $oldDefaultMsg = "ba4463210c11f197661e9fe9b6bbf8cd";
        $templateTitle = "Cancellation Request Confirmation";
        $template = \WHMCS\Mail\Template::master()->where("name", $templateTitle)->first();
        if(is_null($template)) {
            return $this;
        }
        if(md5($template->message) === $oldDefaultMsg) {
            $template->message = trim(self::CANCELLATION_REQUEST_CONFIRMATION);
            $template->save();
            logActivity(sprintf("Email Template `%s` Updated", $templateTitle));
        }
        return $this;
    }
    public function createOnDemandRenewalsTable() : \self
    {
        $createOnDemandTable = "CREATE TABLE IF NOT EXISTS `tblondemandrenewals` (    `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,    `rel_type` enum('Product', 'Addon') NOT NULL,    `rel_id` int NOT NULL DEFAULT '0',    `enabled` tinyint NOT NULL DEFAULT '0',    `monthly` tinyint NOT NULL DEFAULT '0',    `quarterly` tinyint NOT NULL DEFAULT '0',    `semiannually` smallint NOT NULL DEFAULT '0',    `annually` smallint NOT NULL DEFAULT '0',    `biennially` smallint NOT NULL DEFAULT '0',    `triennially` smallint NOT NULL DEFAULT '0',    UNIQUE KEY `tblondemandrenewals_rel_type_rel_id_unique` (`rel_type`, `rel_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        \WHMCS\Database\Capsule::statement($createOnDemandTable);
        return $this;
    }
    public function updateOrderRenewalsFormat() : \self
    {
        \WHMCS\Database\Capsule::table("tblorders")->where("renewals", "!=", "")->chunkById(500, function ($orders) {
            foreach ($orders as $order) {
                $renewalsAsObj = json_decode($order->renewals);
                if(is_null($renewalsAsObj) && json_last_error() !== JSON_ERROR_NONE) {
                    $domainsAsArray = explode(",", $order->renewals);
                    if(is_array($domainsAsArray)) {
                        \WHMCS\Database\Capsule::table("tblorders")->where("id", $order->id)->update(["renewals" => \WHMCS\Order\Order::packRawRenewals($domainsAsArray, [], [])]);
                    }
                }
            }
        });
        return $this;
    }
}

?>