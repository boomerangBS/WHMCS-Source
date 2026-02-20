<?php

namespace WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult;

class CaptureResult extends \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
{
    const REASON_PAYER_ACTION_REQUIRED = "payerActionRequired";
    public function fromApiResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\OrderResponseInterface $captureResponse)
    {
        if($captureResponse->status == "PAYER_ACTION_REQUIRED") {
            $this->rawApiResponse = $captureResponse;
            return $this->setNotSuccessful()->setStatus("declined")->setReason(self::REASON_PAYER_ACTION_REQUIRED)->setDeclineReason(\WHMCS\Module\Gateway\paypal_acdc\Logger::THREE_D_SECURE_REQUIRED)->setResult("data", $captureResponse);
        }
        return parent::fromApiResponse($captureResponse);
    }
    public function setCardDetail($number, string $expiry, string $type) : \self
    {
        return $this->setResult("cardnumber", $number)->setResult("cardexpiry", $expiry)->setResult("cardtype", $type);
    }
    public function is3DSRequired()
    {
        return $this->status() == "declined" && $this->getResult("reason") == self::REASON_PAYER_ACTION_REQUIRED;
    }
}

?>