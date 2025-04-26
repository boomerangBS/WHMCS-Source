<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version8100beta1 extends IncrementalVersion
{
    protected $updateActions = ["createSitejetBuilderWelcomeEmail"];
    public function createSitejetBuilderWelcomeEmail()
    {
        $templateExists = \WHMCS\Mail\Template::where("name", "Sitejet Builder Welcome Email")->first();
        if(!$templateExists) {
            $mailTemplate = new \WHMCS\Mail\Template();
            $mailTemplate->name = "Sitejet Builder Welcome Email";
            $mailTemplate->subject = "Welcome to Sitejet Builder, Your Professional Website Builder";
            $mailTemplate->language = "";
            $mailTemplate->plaintext = false;
            $mailTemplate->custom = false;
            $mailTemplate->type = "product";
            $mailTemplate->message = "<p>Dear {\$client_name},</p>\n<p>Congratulations!</p>\n<p>Your account has been set up and you are ready to begin building your website.</p>\n<p>You can directly access Sitejet Builder and edit your website from many places within the <a href=\"{\$whmcs_url}clientarea.php\">client area</a>, including the homepage.</p>\n<p>If you need any further assistance, please contact our <a href=\"{\$whmcs_url}submitticket.php\">support team</a>.</p>\n<p>{\$signature}</p>";
            $mailTemplate->save();
        }
        return $this;
    }
}

?>