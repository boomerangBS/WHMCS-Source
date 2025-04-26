<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Composite;

interface ViewInterface
{
    public function init();
    public function make();
    public function getTemplate();
    public function withBaseData($data);
    public function with($data);
    public function data() : \Illuminate\Support\Collection;
    public function render();
}

?>