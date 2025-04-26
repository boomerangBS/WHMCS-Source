<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View;

class Bootstrap
{
    public function renderKeyValuePairsInColumns($colWidth, $data)
    {
        $output = "<div class=\"row\">";
        foreach ($data as $values) {
            $output .= "<div class=\"col-sm-" . $colWidth . "\">";
            foreach ($values as $key => $value) {
                if(empty($value)) {
                    $value = "-";
                }
                $output .= $key . ": " . $value . "<br>";
            }
            $output .= "</div>";
        }
        $output .= "</div>";
        return $output;
    }
}

?>