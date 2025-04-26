<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Client;

class HomepagePanel
{
    protected $name;
    protected $label;
    protected $icon;
    protected $color = "blue";
    protected $order = 0;
    protected $bodyHtml;
    protected $buttonLink;
    protected $buttonText;
    protected $buttonIcon;
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
    public function setBodyHtml($bodyHtml)
    {
        $this->bodyHtml = $bodyHtml;
        return $this;
    }
    public function setHeaderButton($link, $text, $icon = "")
    {
        $this->buttonLink = $link;
        $this->buttonText = $text;
        $this->buttonIcon = $icon;
        return $this;
    }
    public function getBodyHtml()
    {
        return $this->bodyHtml;
    }
    public function toArray()
    {
        return ["name" => $this->getName(), "label" => $this->label, "icon" => $this->icon, "order" => $this->order, "bodyHtml" => $this->getBodyHtml(), "extras" => ["color" => $this->color, "btn-link" => $this->buttonLink, "btn-text" => $this->buttonText, "btn-icon" => $this->buttonIcon]];
    }
}

?>