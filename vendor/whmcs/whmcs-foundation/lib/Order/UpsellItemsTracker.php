<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Order;

class UpsellItemsTracker
{
    public function incrementItemUpsellChain($upsellProductId, int $productId) : \self
    {
        $upsellSession = $this->getUpsellData();
        $upsellChain = $upsellSession[$productId] ?? [];
        $upsellChain[] = $productId;
        $upsellSession[$upsellProductId] = $upsellChain;
        $this->setUpsellData($upsellSession);
        return $this;
    }
    public function clearUpsellChain() : \self
    {
        return $this->setUpsellData([]);
    }
    public function getUpsellDataForItem(int $productId, $asString = false)
    {
        $upsellSession = $this->getUpsellData();
        if(array_key_exists($productId, $upsellSession)) {
            return $asString ? implode(",", $upsellSession[$productId]) : $upsellSession[$productId];
        }
        return $asString ? "" : [];
    }
    protected function getUpsellData() : array
    {
        return (array) \WHMCS\Session::get("upsell");
    }
    protected function setUpsellData($data) : \self
    {
        \WHMCS\Session::set("upsell", $data);
        return $this;
    }
}

?>