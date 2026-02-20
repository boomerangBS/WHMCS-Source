<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
abstract class AbstractPaymentSource
{
    protected $paymentType = "paypal";
    protected abstract function getDetails();
    public function get() : array
    {
        return [$this->paymentType => $this->getDetails()];
    }
    public static function factory(string $type)
    {
        $handlerClass = self::paymentSourceClass($type);
        $fqClass = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\Entity" . "\\" . $handlerClass;
        if(!class_exists($fqClass)) {
            throw new \RuntimeException("Class " . $fqClass . " not found");
        }
        return new $fqClass();
    }
    protected static function paymentSourceClass($type)
    {
        return sprintf("%sPaymentSource", ucfirst($type));
    }
}

?>