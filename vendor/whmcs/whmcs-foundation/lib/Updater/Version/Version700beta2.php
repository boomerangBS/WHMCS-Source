<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version700beta2 extends IncrementalVersion
{
    protected $updateActions = ["migrateSystemSslUrl", "decodeEmailTemplates"];
    public function migrateSystemSslUrl()
    {
        $systemSslUrl = trim(\WHMCS\Config\Setting::getValue("SystemSSLURL"));
        if(!empty($systemSslUrl)) {
            \WHMCS\Config\Setting::setValue("SystemURL", $systemSslUrl);
        }
        $setting = \WHMCS\Config\Setting::find("SystemSSLURL");
        if($setting) {
            $setting->delete();
        }
        return $this;
    }
    public function decodeEmailTemplates()
    {
        $emails = \WHMCS\Mail\Template::all();
        foreach ($emails as $email) {
            $email->subject = \WHMCS\Input\Sanitize::decode($email->subject);
            $email->message = \WHMCS\Input\Sanitize::decode($email->message);
            $email->save();
        }
        return $this;
    }
}

?>