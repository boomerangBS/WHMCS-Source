<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Adapter;

interface AdapterInterface
{
    public function getConfigurationParameters();
    public function setConfigurationParameters(array $configuration);
    public function getSolutionType();
    public function setSolutionType($type);
    public function isLinkCapable();
    public function isCaptureCapable();
    public function isRefundCapable();
    public function isRemotePaymentDetailsStorageCapable();
    public function getHtmlLink(array $params);
    public function captureTransaction(array $params);
    public function refundTransaction(array $params);
    public function storePaymentDetailsRemotely(array $params);
}

?>