<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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