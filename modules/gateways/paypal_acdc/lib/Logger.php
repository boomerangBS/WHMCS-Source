<?php

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