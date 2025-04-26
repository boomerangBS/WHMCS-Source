<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Incoming;

interface MailboxInterface
{
    public static function createForDepartment(\WHMCS\Support\Department $department, $isTest) : MailboxInterface;
    public function getMessageCount() : int;
    public function getAllMessages() : \Iterator;
    public function getRfcMessage($messageIndex, $messageData);
    public function deleteMessage($messageIndex) : void;
    public function close() : void;
}

?>