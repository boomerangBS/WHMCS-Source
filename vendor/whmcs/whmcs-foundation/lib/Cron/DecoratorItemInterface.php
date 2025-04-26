<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron;

interface DecoratorItemInterface
{
    public function getIcon();
    public function getName();
    public function getSuccessCountIdentifier();
    public function getFailureCountIdentifier();
    public function getSuccessKeyword();
    public function getFailureKeyword();
    public function getFailureUrl();
    public function getDetailUrl();
    public function isBooleanStatusItem();
    public function hasDetail();
}

?>