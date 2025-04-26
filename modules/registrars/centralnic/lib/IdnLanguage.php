<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class IdnLanguage
{
    private $api;
    private $isoTagMap = ["ara" => "ar", "arm" => "armn", "aze" => "az", "bel" => "be", "ben" => "beng", "bul" => "bg", "bos" => "bs", "cat" => "ca", "cze" => "cs", "dan" => "da", "ger" => "de", "spa" => "es", "est" => "et", "fin" => "fi", "fao" => "fo", "fre" => "fr", "geo" => "geor", "gre" => "grek", "guj" => "gujr", "heb" => "he", "hin" => "hi", "scr" => "hr", "hun" => "hu", "ice" => "is", "ita" => "it", "jpn" => "ja", "khm" => "khmr", "kor" => "ko", "kur" => "ku", "lat" => "latn", "lao" => "lo", "lit" => "lt", "lav" => "lv", "mac" => "mk", "dut" => "nl", "nor" => "no", "pol" => "pl", "por" => "pt", "rum" => "ro", "rus" => "ru", "slo" => "sk", "slv" => "sl", "alb" => "sq", "scc" => "sr", "swe" => "sv", "tam" => "taml", "tel" => "telu", "tha" => "th", "tur" => "tr", "ukr" => "uk", "chi" => "zh"];
    public function __construct(Api\AbstractApi $api)
    {
        $this->api = $api;
    }
    public function getTagFor(string $tld, string $languageISO)
    {
        $languageList = $this->get($tld);
        $convertedLanguageTag = $this->convertISOToLanguageTag($languageISO);
        if(!is_null($convertedLanguageTag) && in_array($convertedLanguageTag, $languageList)) {
            return $convertedLanguageTag;
        }
        if(in_array($languageISO, $languageList)) {
            return $languageISO;
        }
        return NULL;
    }
    public function get($tld) : array
    {
        $availableLanguages = (new Commands\QueryIDNTagList($this->api, $tld))->execute()->getDataValue("idnlanguagetag");
        if(is_string($availableLanguages)) {
            $availableLanguages = [$availableLanguages];
        }
        return array_map("strtolower", $availableLanguages);
    }
    private function convertISOToLanguageTag(string $languageISO)
    {
        if(isset($this->isoTagMap[$languageISO])) {
            return $this->isoTagMap[$languageISO];
        }
        return NULL;
    }
}

?>