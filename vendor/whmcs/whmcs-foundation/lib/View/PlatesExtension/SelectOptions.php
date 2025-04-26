<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\PlatesExtension;

class SelectOptions implements \League\Plates\Extension\ExtensionInterface
{
    public function register(\League\Plates\Engine $engine)
    {
        $engine->registerFunction("selectOptions", [$this, "selectOptions"]);
        $engine->registerFunction("selectOptionsWithAttributes", [$this, "selectOptionsWithAttributes"]);
    }
    public function selectOptions($valueTextMap, $selectedValue)
    {
        return $this->selectOptionsWithAttributes(collect($valueTextMap)->map(function ($value, $key) {
            return ["value" => $key, "text" => $value];
        }), $selectedValue);
    }
    public function selectOptionsWithAttributes($optionValues, $selectedValue)
    {
        $options = [];
        foreach ($optionValues as $option) {
            $value = $option["value"] ?? "";
            $text = $option["text"] ?? "";
            unset($option["value"]);
            unset($option["text"]);
            $extraAttributes = [];
            foreach ($option as $attrName => $attrValue) {
                $extraAttributes[] = sprintf("%s=\"%s\"", $attrName, $attrValue);
            }
            if($value === $selectedValue) {
                $extraAttributes[] = "selected=\"selected\"";
            }
            $options[] = sprintf("<option value=\"%s\"%s>%s</option>", $value, 0 < count($extraAttributes) ? sprintf(" %s", implode(" ", $extraAttributes)) : "", $text);
        }
        return implode(PHP_EOL, $options);
    }
}

?>