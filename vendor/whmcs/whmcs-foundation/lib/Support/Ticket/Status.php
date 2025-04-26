<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket;

class Status extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblticketstatuses";
    public $timestamps = false;
    const STATUS_OPEN = "Open";
    const STATUS_ANSWERED = "Answered";
    const STATUS_CUSTOMER_REPLY = "Customer-Reply";
    const STATUS_ON_HOLD = "On Hold";
    const STATUS_IN_PROGRESS = "In Progress";
    const STATUS_CLOSED = "Closed";
    public static function getAwaitingReply()
    {
        return self::where("showawaiting", "1")->pluck("title");
    }
    public static function getActive()
    {
        return self::where("showactive", "1")->pluck("title");
    }
    protected function adminLangKeyMap() : array
    {
        return [self::STATUS_OPEN => "supportticketsstatus.open", self::STATUS_ANSWERED => "supportticketsstatus.answered", self::STATUS_CUSTOMER_REPLY => "supportticketsstatus.customerreply", self::STATUS_ON_HOLD => "supportticketsstatus.onhold", self::STATUS_IN_PROGRESS => "supportticketsstatus.inprogress", self::STATUS_CLOSED => "supportticketsstatus.closed"];
    }
    public function adminTitle()
    {
        return static::translateTitleForAdmin($this->title);
    }
    public static function translateTitleForAdmin($value)
    {
        $langKeyMap = (new static())->adminLangKeyMap();
        $langKey = $langKeyMap[$value] ?? NULL;
        if($langKey === NULL) {
            return $value;
        }
        return \AdminLang::trans($langKey);
    }
}

?>