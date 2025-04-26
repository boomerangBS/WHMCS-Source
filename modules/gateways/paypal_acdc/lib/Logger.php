<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc;

class Logger extends \WHMCS\Module\Gateway\paypal_ppcpv\Logger
{
    const THREE_D_SECURE_REQUIRED = "3D Secure authentication required";
    public function module($action, string $request, string $response = "", string $data = [], array $variablesToMask) : void
    {
        logModuleCall(Core::MODULE_NAME, $action, $request, $response, $data, $variablesToMask);
    }
    public function orderDeclineLiability(\WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse $orderStatusResponse) : void
    {
        $this->orderStatus($orderStatusResponse, "Declined", self::THREE_D_SECURE_REQUIRED);
    }
}

?>