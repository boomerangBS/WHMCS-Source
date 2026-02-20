<?php

namespace WHMCS\Billing\Payment;

interface DisputeInterface
{
    public static function factory($id, $amount, string $currencyCode, string $transactionId, $createdDate, $respondBy, string $reason, string $status) : DisputeInterface;
    public function setEvidence($evidence) : DisputeInterface;
    public function setEvidenceType($evidenceKey, string $evidenceType) : DisputeInterface;
    public function setGateway($gateway) : DisputeInterface;
    public function setIsClosable($closable) : DisputeInterface;
    public function setIsSubmittable($submittable) : DisputeInterface;
    public function setIsUpdatable($updatable) : DisputeInterface;
    public function setTransactionId($transactionId) : DisputeInterface;
}

?>