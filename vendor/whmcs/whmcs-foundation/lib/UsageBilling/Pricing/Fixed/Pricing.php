<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Pricing\Fixed;

class Pricing extends \WHMCS\Billing\Pricing
{
    protected $table = "tblpricing_fixed";
    public function bracket()
    {
        $this->belongsTo("WHMCS\\UsageBilling\\Pricing\\Fixed\\Bracket", "id", "relid", "bracket");
    }
    public function pricingType()
    {
        return \WHMCS\Billing\PricingInterface::TYPE_USAGE;
    }
}

?>