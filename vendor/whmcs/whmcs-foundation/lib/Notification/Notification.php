<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Notification;

class Notification implements Contracts\NotificationInterface
{
    protected $title = "";
    protected $message = "";
    protected $url = "";
    protected $attributes = [];
    public function getTitle()
    {
        return $this->title;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
    public function setTitle($title)
    {
        $this->title = trim($title);
        return $this;
    }
    public function setMessage($message)
    {
        $this->message = trim($message);
        return $this;
    }
    public function setUrl($url)
    {
        $this->url = trim($url);
        return $this;
    }
    public function addAttribute(Contracts\NotificationAttributeInterface $attribute)
    {
        $this->attributes[] = $attribute;
        return $this;
    }
}

?>