<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Notification\Slack;

class Message
{
    public $channel = "";
    public $text = "";
    public $asUser = true;
    public $username = "";
    public $attachment;
    public function channel($channel)
    {
        $this->channel = trim($channel);
        return $this;
    }
    public function text($text)
    {
        $this->text = trim($text);
        return $this;
    }
    public function username($username)
    {
        $this->asUser = false;
        $this->username = trim($username);
        return $this;
    }
    public function attachment($attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }
    public function toArray()
    {
        $message = ["channel" => $this->channel, "text" => $this->text, "username" => $this->username];
        if(!empty($this->attachment)) {
            $message["attachments"] = json_encode([$this->attachment->toArray()]);
        }
        return $message;
    }
}

?>