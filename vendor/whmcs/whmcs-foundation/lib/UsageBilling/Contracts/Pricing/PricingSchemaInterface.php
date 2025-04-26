<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Contracts\Pricing;

interface PricingSchemaInterface extends \WHMCS\Contracts\CollectionInterface
{
    const TYPE_SIMPLE = "simple";
    const TYPE_FLAT = "flat";
    const TYPE_GRADUATED = "grad";
    public function getStubInclusiveBracket();
    public static function getSchemaTypes();
    public function schemaType();
    public function isFree();
    public function freeLimit();
    public function firstCostBracket();
    public function fixedUsagePricing();
}

?>