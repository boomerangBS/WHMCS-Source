<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Notification\Slack;

class Field
{
    public $title = "";
    public $value = "";
    public $short = false;
    public function title($title)
    {
        $this->title = trim($title);
        return $this;
    }
    public function value($value)
    {
        $this->value = trim($value);
        return $this;
    }
    public function short()
    {
        $this->short = true;
        return $this;
    }
    public function toArray()
    {
        $field = ["title" => $this->title, "value" => $this->value, "short" => $this->short];
        return $field;
    }
}

?>