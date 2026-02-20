<?php

namespace WHMCS\Module\Gateway\Stripe\Admin;

class Warning
{
    protected static $warning = "You must update your :gateway configuration to use the correct keys. :clickHere to update the configuration.";
    const EPOCH_CHECK_KEY = "stripeEpochFrom";
    public static function message($gateway) : array
    {
        try {
            $epochFrom = \WHMCS\Config\Setting::getValue(self::EPOCH_CHECK_KEY);
            if(!$epochFrom) {
                $epochFrom = \WHMCS\Carbon::now()->toDateTimeString();
                \WHMCS\Config\Setting::setValue(self::EPOCH_CHECK_KEY, $epochFrom);
            }
            $epochFrom = \WHMCS\Carbon::createFromFormat("Y-m-d H:i:s", $epochFrom);
            if(!$epochFrom->addDays(4)->isPast()) {
                return [];
            }
            $stripeInterface = \WHMCS\Module\Gateway::factory($gateway);
            $secretKey = $stripeInterface->getParams()["secretKey"] ?? NULL;
            if(!$secretKey || substr($secretKey, 0, 2) === "rk") {
                return [];
            }
            $url = \App::getSystemURL() . \App::get_admin_folder_name();
            $url .= "/configgateways.php?manage=" . $gateway . "#m_" . $gateway;
            $prepend = "<i class=\"far fa-info-circle fa-fw\"></i>";
            $message = str_replace([":gateway", ":clickHere"], [$stripeInterface->getDisplayName(), "<a href=\"" . $url . "\">Click here</a>"], self::$warning);
            return ["frequency" => 604800, "output" => $prepend . $message];
        } catch (\Throwable $t) {
            return [];
        }
    }
}

?>