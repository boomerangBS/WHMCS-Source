<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Notification\Hipchat;

class CardAttribute
{
    public $value;
    public $label;
    public $url;
    public $style;
    public $icon;
    public $icon2;
    public function value($value)
    {
        $this->value = trim($value);
        return $this;
    }
    public function label($label)
    {
        $this->label = trim($label);
        return $this;
    }
    public function url($url)
    {
        $this->url = trim($url);
        return $this;
    }
    public function style($style)
    {
        if($style == "success") {
            $style = "lozenge-success";
        } elseif($style == "danger") {
            $style = "lozenge-error";
        } elseif($style == "warning") {
            $style = "lozenge-current";
        } elseif($style == "info") {
            $style = "lozenge-complete";
        } elseif($style == "primary") {
            $style = "lozenge";
        } else {
            return $this;
        }
        $this->style = trim($style);
        return $this;
    }
    public function icon($icon, $icon2 = NULL)
    {
        $this->icon = trim($icon);
        if(!empty($icon2)) {
            $this->icon2 = trim($icon2);
        }
        return $this;
    }
    public function toArray()
    {
        $attribute = ["value" => array_filter(["label" => $this->value, "url" => $this->url, "style" => $this->style])];
        if(!empty($this->icon)) {
            $attribute["value"]["icon"] = array_filter(["url" => $this->icon, "url@2x" => $this->icon2]);
        }
        if(!empty($this->label)) {
            $attribute["label"] = $this->label;
        }
        return $attribute;
    }
}

?>