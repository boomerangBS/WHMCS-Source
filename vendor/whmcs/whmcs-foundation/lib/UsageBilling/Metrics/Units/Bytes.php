<?php

namespace WHMCS\UsageBilling\Metrics\Units;

class Bytes extends FloatingPoint
{
    public function __construct($name = "Bytes", $singlePerUnitName = "Byte", $pluralPerUnitName = "Bytes", $prefix = NULL, $suffix = "B")
    {
        parent::__construct($name, $singlePerUnitName, $pluralPerUnitName, $prefix, $suffix);
    }
    public static function convert($value, $from, $to)
    {
        $result = $value;
        if($from == "B") {
            if($to == "KB") {
                $result = $value / 1024;
            } elseif($to == "MB") {
                $result = $value / 1024 / 1024;
            } elseif($to == "GB") {
                $result = $value / 1024 / 1024 / 1024;
            }
        } elseif($from == "KB") {
            if($to == "B") {
                $result = $value * 1024;
            } elseif($to == "MB") {
                $result = $value / 1024;
            } elseif($to == "GB") {
                $result = $value / 1024 / 1024;
            }
        } elseif($from == "MB") {
            if($to == "B") {
                $result = $value * 1024 * 1024;
            } elseif($to == "KB") {
                $result = $value * 1024;
            } elseif($to == "GB") {
                $result = $value / 1024;
            }
        } elseif($from == "GB") {
            if($to == "B") {
                $result = $value * 1024 * 1024 * 1024;
            } elseif($to == "KB") {
                $result = $value * 1024 * 1024;
            } elseif($to == "MB") {
                $result = $value * 1024;
            }
        }
        return $result;
    }
}

?>