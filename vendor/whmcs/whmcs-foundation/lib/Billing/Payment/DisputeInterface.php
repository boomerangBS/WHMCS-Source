<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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