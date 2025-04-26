<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\TCO;

class Helper
{
    protected static $languages = ["chinese" => "zh", "danish" => "da", "dutch" => "nl", "french" => "fr", "german" => "gr", "greek" => "el", "italian" => "it", "japanese" => "jp", "norwegian" => "no", "portuguese" => "pt", "slovenian" => "sl", "spanish" => "es_la", "swedish" => "sv", "english" => "en"];
    public static function convertCurrency($amount, \WHMCS\Billing\Currency $currency, \WHMCS\Billing\Invoice $invoice)
    {
        return \WHMCS\Billing\Invoice\Helper::convertCurrency($amount, $currency, $invoice);
    }
    public static function language($language)
    {
        $language = strtolower($language);
        $tcoLanguage = "";
        if(array_key_exists($language, self::$languages)) {
            $tcoLanguage = self::$languages[$language];
        }
        return $tcoLanguage;
    }
    public static function languageInput($language)
    {
        $tcoLanguage = self::language($language);
        if($tcoLanguage) {
            $tcoLanguage = "<input type=\"hidden\" name=\"lang\" value=\"" . $tcoLanguage . "\">";
        }
        return $tcoLanguage;
    }
    public static function isValidHash($hashInput, string $receivedHash)
    {
        return collect(["md5", "sha256", "sha3-256"])->contains(function ($algo) use($hashInput, $receivedHash) {
            $hash = strtoupper(hash($algo, $hashInput));
            return hash_equals($hash, $receivedHash);
        });
    }
}

?>