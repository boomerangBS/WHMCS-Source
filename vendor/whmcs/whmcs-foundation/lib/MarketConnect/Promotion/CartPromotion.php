<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Promotion;

class CartPromotion extends Promotion
{
    protected function getTemplate()
    {
        $orderFormTemplate = \WHMCS\View\Template\OrderForm::factory("marketconnect-promo.tpl");
        return $orderFormTemplate->getTemplatePath() . "marketconnect-promo.tpl";
    }
}

?>