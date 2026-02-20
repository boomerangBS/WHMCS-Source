<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4C6F676765722E7068703078376664353934323461383566_
{
    public $moduleName = "";
    public $packedOrderResponse = "";
}
class Logger
{
    protected $moduleConfiguration;
    protected $module;
    public static function factory(ModuleConfiguration $config, $module) : \self
    {
        return new static($config, $module);
    }
    public function __construct(ModuleConfiguration $config, \WHMCS\Module\Gateway $module)
    {
        $this->moduleConfiguration = $config;
        $this->module = $module;
    }
    public function activity($description = 0, int $clientId = [], array $opts) : void
    {
        $prefix = "[" . $this->moduleConfiguration->getGatewayName() . "]";
        $description = $prefix . " " . $description;
        logActivity($description, $clientId, $opts);
    }
    public function gateway($data, string $result = [], array $passedParams) : void
    {
        logTransaction($this->moduleConfiguration->getGatewayDefaultName(), $data, $result, $passedParams, NULL);
    }
    public function gatewayOrder(API\OrderResponseInterface $orderResponse, string $resultLabel) : void
    {
        $this->gateway(Util::decodeJSON($orderResponse->packOrderResponse()), $resultLabel);
    }
    public function gatewayCapture(API\OrderResponseInterface $capturedOrderResponse, $history) : void
    {
        $this->gateway(Util::decodeJSON($capturedOrderResponse->packOrderResponse()), $this->getFriendlyStatus($capturedOrderResponse->captureData()->status), ["history_id" => $history->id]);
    }
    protected function getFriendlyStatus($status)
    {
        $friendlyStatus = "Incomplete";
        switch ($status) {
            case "COMPLETED":
                $friendlyStatus = "Success";
                break;
            case "PENDING":
                $friendlyStatus = "Pending";
                break;
            case "DECLINED":
                $friendlyStatus = "Declined";
                break;
            default:
                return $friendlyStatus;
        }
    }
    public function historyCapture(API\OrderResponseInterface $capturedOrderResponse, $invoice) : void
    {
        $this->gatewayCapture($capturedOrderResponse, $this->history($capturedOrderResponse, $invoice));
    }
    protected function orderStatus(API\OrderStatusResponse $orderStatusResponse = NULL, string $status = NULL, string $prependedReason) : void
    {
        $packedOrderResponse = Util::decodeJSON($orderStatusResponse->packOrderResponse());
        if(!is_null($prependedReason)) {
            $packedOrderResponse = (object) array_merge(["Reason" => $prependedReason], (array) $packedOrderResponse);
        }
        $this->gateway($packedOrderResponse, $status ?? $this->getFriendlyStatus($orderStatusResponse->status));
    }
    public function history(API\OrderResponseInterface $apiResponse, $invoice) : \WHMCS\Billing\Payment\Transaction\History
    {
        $history = new \WHMCS\Billing\Payment\Transaction\History();
        $history->invoiceId = $invoice->id;
        $history->gateway = $this->moduleConfiguration->getGatewayName();
        $history->additionalInformation = $this->module->getLoadedModule() . "|" . $apiResponse->packOrderResponse();
        $captureData = $apiResponse->captureData();
        if(is_null($captureData)) {
            $history->save();
            return $history;
        }
        $history->transactionId = $captureData->id;
        $history->remoteStatus = $captureData->status;
        $history->description = $captureData->status_details->reason ?? "";
        $history->completed = $captureData->status == "COMPLETED";
        $history->amount = $captureData->amount->value;
        $history->currencyId = Util::safeLoadCurrencyId($captureData->amount->currency_code);
        $history->save();
        return $history;
    }
    public static function historyUnpackAdditional(string $additionalInformation)
    {
        $pos = strpos($additionalInformation, "|{");
        if($pos === false) {
            return $additionalInformation;
        }
        $dataObj = new func_num_args();
        $moduleName = substr($additionalInformation, 0, $pos);
        $data = substr($additionalInformation, $pos + 1);
        $dataObj->moduleName = $moduleName;
        $dataObj->packedOrderResponse = $data;
        return $dataObj;
    }
    public function module($action, string $request, string $response = "", string $data = [], array $variablesToMask) : void
    {
        logModuleCall(PayPalCommerce::MODULE_NAME, $action, $request, $response, $data, $variablesToMask);
    }
}

?>