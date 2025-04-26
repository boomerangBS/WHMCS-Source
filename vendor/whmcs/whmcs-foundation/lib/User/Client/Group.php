<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Client;

class Group extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblclientgroups";
    public $timestamps = false;
    protected $columnMap = ["name" => "groupname", "color" => "groupcolour", "discountPercentage" => "discountpercent", "exemptFromAutomation" => "susptermexempt"];
    protected $casts = ["susptermexempt" => "bool"];
    public function clients()
    {
        return $this->hasMany("WHMCS\\User\\Client", "groupid", "id");
    }
}

?>