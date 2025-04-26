<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions;

class SkipFlagMask extends \WHMCS\Utility\Bitmask
{
    const BIT_COUNT = 6;
    const SKIP_ON_ADMIN_REPLY = 1;
    const SKIP_ON_OWNER_REPLY = 2;
    const SKIP_ON_USER_REPLY = 4;
    const SKIP_ON_REGISTERED_USER_REPLY = 8;
    const SKIP_ON_LEGACY_SUBACCOUNT_REPLY = 16;
    const SKIP_ON_GUEST_REPLY = 32;
    const SKIP_ON_ANY_REPLY = NULL;
    const SKIP_ON_NONADMIN_REPLY = NULL;
    public static function factoryAsSkipOnNonAdminReply() : \self
    {
        return new static(self::SKIP_ON_NONADMIN_REPLY);
    }
    public static function factoryAsSkipOnAnyReply() : \self
    {
        return new static(self::SKIP_ON_ANY_REPLY);
    }
    public function isSkipOnNonAdminReply()
    {
        return $this->has(self::SKIP_ON_NONADMIN_REPLY);
    }
    public function isSkipOnAnyReply()
    {
        return $this->has(self::SKIP_ON_ANY_REPLY);
    }
}

?>