<?php

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