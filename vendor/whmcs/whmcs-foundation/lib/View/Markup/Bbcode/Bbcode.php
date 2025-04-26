<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Bbcode;

class Bbcode
{
    public static function transform($text)
    {
        $bbCodeMap = ["b" => "strong", "i" => "em", "u" => "ul", "div" => "div"];
        $text = preg_replace("/\\[div=(&quot;|\")(.*?)(&quot;|\")\\]/", "<div class=\"\$2\">", $text);
        foreach ($bbCodeMap as $bbCode => $htmlCode) {
            $text = str_replace("[" . $bbCode . "]", "<" . $htmlCode . ">", $text);
            $text = str_replace("[/" . $bbCode . "]", "</" . $htmlCode . ">", $text);
        }
        return $text;
    }
}

?>