<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Incoming;

class MailboxFactory
{
    const PROPRIETARY_TRANSPORT_MAILBOX_CLASSES = NULL;
    public static function createForDepartment(\WHMCS\Support\Department $department = false, $isTest) : MailboxInterface
    {
        $mailProvider = $department->mailAuthConfig["service_provider"];
        $mailboxClass = self::PROPRIETARY_TRANSPORT_MAILBOX_CLASSES[$mailProvider] ?? "WHMCS\\Mail\\Incoming\\Mailbox";
        return $mailboxClass::createForDepartment($department, $isTest);
    }
}

?>