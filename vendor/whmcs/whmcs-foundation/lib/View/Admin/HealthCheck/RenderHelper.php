<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\View\Admin\HealthCheck;

// Decoded file for php version 72.
class RenderHelper
{
    public function section($title)
    {
        return sprintf("<strong>%s</strong><br/>", $title);
    }
    public function unordered($items, callable $renderer) : array
    {
        $out = "<ul>";
        foreach ($items as $item) {
            $out .= $this->li($renderer($item)) . "\n";
        }
        return $out . "</ul>";
    }
    public function li($item)
    {
        return sprintf("<li>%s</li>", $item);
    }
}

?>