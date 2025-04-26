<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\GoCardless;

class GoCardless
{
    const SUPPORTED_CURRENCIES = ["AUD", "CAD", "DKK", "EUR", "GBP", "NZD", "SEK", "USD"];
    const SCHEMES = ["AUD" => "becs", "CAD" => "pad", "DKK" => "betalingsservice", "EUR" => "sepa", "GBP" => "bacs", "NZD" => "becs_nz", "SEK" => "autogiro", "USD" => "ach"];
    const SCHEME_NAMES = ["becs" => "BECS", "pad" => "PAD", "betalingsservice" => "Betalingsservice", "sepa" => "SEPA", "bacs" => "BACS", "becs_nz" => "BECS NZ", "autogiro" => "Autogiro", "ach" => "ACH"];
}

?>