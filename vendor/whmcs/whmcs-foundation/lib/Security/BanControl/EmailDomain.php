<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Security\BanControl;

class EmailDomain extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblbannedemails";
    protected $attributes = ["count" => 0];
    protected $fillable = ["domain", "count"];
    public $timestamps = false;
}

?>