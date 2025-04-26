<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Language;

class TranslatorBridge implements \Illuminate\Contracts\Translation\Translator
{
    private $language;
    public function __construct(AbstractLanguage $language)
    {
        $this->language = $language;
    }
    public function get($key, array $replace = [], $locale = NULL)
    {
        return $this->language->trans($key, $replace);
    }
    public function choice($key, $number, array $replace = [], $locale = NULL)
    {
        return $this->get($key, $replace, $locale);
    }
    public function getLocale()
    {
        return $this->language->getLocale();
    }
    public function setLocale($locale)
    {
        throw new \WHMCS\Exception("Not implemented");
    }
}

?>