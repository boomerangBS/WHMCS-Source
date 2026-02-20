<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult;

class CaptureResult
{
    protected $data;
    protected $rawApiResponse;
    const REASON_CAPTURE_STATUS_PENDING = "captureStatusPending";
    const REASON_ORDER_STATUS_INCOMPLETE = "orderStatusIncomplete";
    const REASON_CAPTURE_STATUS_INCOMPLETE = "captureStatusIncomplete";
    protected function setResult($key, $value) : \self
    {
        $this->data[$key] = $value;
        return $this;
    }
    protected function getResult(string $key)
    {
        return $this->data[$key];
    }
    public function fromApiResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\OrderResponseInterface $captureResponse)
    {
        $this->rawApiResponse = $captureResponse;
        switch ($captureResponse->status) {
            case "COMPLETED":
                $this->fromApiCompletedApiResponse($captureResponse);
                break;
            case "DECLINED":
                $this->setNotSuccessful()->setStatus("declined")->setResult("data", $captureResponse);
                return $this;
                break;
            default:
                return $this->setNotSuccessful()->setStatus("error")->setReason(self::REASON_ORDER_STATUS_INCOMPLETE)->setResult("data", $captureResponse);
        }
    }
    protected function fromAPICompletedApiResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\OrderResponseInterface $captureResponse) : void
    {
        $purchaseUnits = $captureResponse->purchase_units;
        if(1 < count($purchaseUnits) || 1 < count($purchaseUnits[0]->payments->captures)) {
            $this->setNotSuccessful()->setStatus("error")->setReason("Unexpected number of purchase units or captures: " . count($purchaseUnits));
        }
        $captureStatus = $captureResponse->captureData()->status ?? "";
        switch ($captureStatus) {
            case "COMPLETED":
                $this->setSuccessful()->setStatus("success");
                break;
            case "DECLINED":
                $this->setNotSuccessful()->setStatus("declined");
                break;
            case "PENDING":
                $this->setSuccessful()->setStatus("pending")->setReason(self::REASON_CAPTURE_STATUS_PENDING);
                break;
            default:
                $this->setNotSuccessful()->setReason(self::REASON_CAPTURE_STATUS_INCOMPLETE)->setStatus(strtolower($captureStatus));
        }
    }
    public function getRawApiResponse()
    {
        return $this->rawApiResponse;
    }
    public function get() : array
    {
        return $this->data;
    }
    public function setStatus($status) : \self
    {
        return $this->setResult("status", $status);
    }
    public function setSuccessful() : \self
    {
        return $this->setResult("success", true);
    }
    public function setNotSuccessful() : \self
    {
        return $this->setResult("success", false);
    }
    public function setReason($reason) : \self
    {
        return $this->setResult("reason", $reason);
    }
    protected function setDeclineReason($reason) : \self
    {
        return $this->setResult("declinereason", $reason);
    }
    public function setRedirectUrl($url) : \self
    {
        return $this->setResult("redirectUrl", $url);
    }
    public function getRedirectUrl()
    {
        return $this->getResult("redirectUrl");
    }
    public function setRawData($apiResponse) : \self
    {
        return $this->setResult("rawdata", $apiResponse);
    }
    public function setData($data) : \self
    {
        return $this->setResult("data", $data);
    }
    public function setTransactionId($transId) : \self
    {
        return $this->setResult("transid", $transId);
    }
    public function setPayPalDetail(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedTokenPayPal $vaultedToken) : \self
    {
        return $this->setResult("cardhint", $vaultedToken->payPalEmail())->setResult("cardtype", $vaultedToken->brand())->setResult("cardexpiry", $vaultedToken->cardExpiry()->format("m/Y"))->setResult("cardnumber", $vaultedToken->payPalEmail());
    }
    public function setGatewayId($gatewayId) : \self
    {
        return $this->setResult("gatewayid", $gatewayId);
    }
    public function setAmount($amount) : \self
    {
        return $this->setResult("amount", $amount);
    }
    public function setFee($fee) : \self
    {
        return $this->setResult("fee", $fee);
    }
    public function setTransactionHistoryId($historyId) : \self
    {
        return $this->setResult("history_id", $historyId);
    }
    public function getTransactionHistoryId() : int
    {
        return $this->getResult("history_id");
    }
    public function orderIdentifier()
    {
        $orderResponse = $this->getOrderResponse();
        if(!is_null($orderResponse)) {
            return $orderResponse->id;
        }
        return NULL;
    }
    public function status()
    {
        return $this->data["status"] ?? "";
    }
    public function isComplete()
    {
        return $this->data["success"] && $this->status() == "success";
    }
    public function isError()
    {
        return $this->status() == "error";
    }
    public function isPending()
    {
        return $this->status() === "pending";
    }
    public function isCapturePending()
    {
        return $this->isPending() && $this->getResult("reason") == self::REASON_CAPTURE_STATUS_PENDING;
    }
    public function getDeclineReason()
    {
        return $this->data["declinereason"] ?? "";
    }
    public function prependRawData($key, string $value) : \self
    {
        $this->setRawData((object) array_merge([$key => $value], (array) $this->getResult("rawdata")));
        return $this;
    }
}

?>