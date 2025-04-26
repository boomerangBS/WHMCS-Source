<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

class CancellationRequest extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblcancelrequests";
    protected $columnMap = ["serviceId" => "relid", "whenToCancel" => "type"];
    protected $dates = ["date"];
    public function service()
    {
        return $this->belongsTo("WHMCS\\Service\\Service", "relid", "id", "service");
    }
}

?>