<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F48616E646C65722F4576656E742F4162737472616374576562686F6F6B48616E646C65722E7068703078376664353934323461353638_
{
    public $transactionHistoryId;
}
abstract class AbstractWebhookHandler
{
    public abstract function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent;
    public static function newOutcomes()
    {
        return new func_num_args();
    }
    public static function moduleNameByTransactionHistory(\WHMCS\Billing\Payment\Transaction\History $transactionHistory) : \WHMCS\Billing\Payment\Transaction\History
    {
        if(is_null($transactionHistory)) {
            return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        }
        $unpackedData = \WHMCS\Module\Gateway\paypal_ppcpv\Logger::historyUnpackAdditional($transactionHistory->additionalInformation);
        if(is_object($unpackedData)) {
            return $unpackedData->moduleName;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    }
}

?>