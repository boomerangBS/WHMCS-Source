<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
trait RequestSendReadyAllPropertiesTrait
{
    public function sendReady()
    {
        $class = new \ReflectionClass($this);
        foreach ($class->getProperties() as $property) {
            if($property->getDeclaringClass()->getName() != self::class) {
            } else {
                $property->setAccessible(true);
                $value = $property->getValue($this);
                $empty = true;
                gettype($value);
                switch (gettype($value)) {
                    case "string":
                        $empty = strlen($value) == 0;
                        break;
                    case "array":
                        $empty = count($value) == 0;
                        break;
                    default:
                        $empty = is_null($value);
                        if($empty) {
                            return false;
                        }
                }
            }
        }
        return true;
    }
}

?>