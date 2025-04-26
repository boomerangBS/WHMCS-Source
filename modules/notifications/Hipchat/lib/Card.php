<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Notification\Hipchat;

class Card
{
    public $id;
    public $title = "";
    public $style = "application";
    public $content = "";
    public $format = "html";
    public $cardFormat;
    public $url = "";
    public $activity;
    public $activityIcon;
    public $activityIcon2;
    public $icon;
    public $icon2;
    public $attributes = [];
    public function __construct()
    {
        $this->id = \Illuminate\Support\Str::random();
    }
    public function title($title)
    {
        $this->title = trim($title);
        return $this;
    }
    public function id($id)
    {
        $this->id = trim($id);
        return $this;
    }
    public function style($style)
    {
        $this->style = $style;
        return $this;
    }
    public function message($content = "")
    {
        $this->content = $content;
        return $this;
    }
    public function cardFormat($cardFormat)
    {
        $this->cardFormat = trim($cardFormat);
        return $this;
    }
    public function url($url)
    {
        $this->url = trim($url);
        return $this;
    }
    public function activity($html, $icon = NULL, $icon2 = NULL)
    {
        $this->activity = trim($html);
        if(!empty($icon)) {
            $this->activityIcon = trim($icon);
        }
        if(!empty($icon2)) {
            $this->activityIcon2 = trim($icon2);
        }
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
    public function addAttribute(CardAttribute $attribute)
    {
        $this->attributes[] = $attribute;
        return $this;
    }
    public function toArray()
    {
        $card = array_filter(["id" => $this->id, "style" => $this->style, "format" => $this->cardFormat, "title" => $this->title, "url" => $this->url]);
        if(!empty($this->content)) {
            $card["description"] = ["value" => $this->content, "format" => $this->format];
        }
        if(!empty($this->activity)) {
            $card["activity"] = array_filter(["html" => $this->activity, "icon" => array_filter(["url" => $this->activityIcon, "url@2x" => $this->activityIcon2])]);
        }
        if(!empty($this->icon)) {
            $card["icon"] = array_filter(["url" => $this->icon, "url@2x" => $this->icon2]);
        }
        if(!empty($this->attributes)) {
            $card["attributes"] = array_map(function (CardAttribute $attribute) {
                return $attribute->toArray();
            }, $this->attributes);
        }
        return $card;
    }
}

?>