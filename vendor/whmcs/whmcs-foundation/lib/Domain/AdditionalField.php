<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Domain;

class AdditionalField extends \WHMCS\Model\AbstractModel
{
    protected $table = "tbldomainsadditionalfields";
    protected $fillable = ["domainid", "name"];
    public function domain()
    {
        return $this->belongsTo("WHMCS\\Domain\\Domain", "domainid", "id", "domain");
    }
}

?>