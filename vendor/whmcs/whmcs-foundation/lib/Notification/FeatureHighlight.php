<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Notification;

class FeatureHighlight
{
    protected $title;
    protected $subtitle;
    protected $headlineImage;
    protected $iconImage;
    protected $description;
    protected $btn1Link;
    protected $btn1Label;
    protected $btn2Link;
    protected $btn2Label;
    protected $assetHelper;
    protected $iconBackgroundEnabled = true;
    public function __construct($title = NULL, $subtitle = NULL, $headlineImage = NULL, $iconImage = NULL, $description = NULL, $btn1Link = NULL, $btn1Label = NULL, $btn2Link = NULL, $btn2Label = NULL)
    {
        if(empty($title)) {
            throw new \WHMCS\Exception("FeatureHighlight Entities are required to have a title.");
        }
        if(empty($subtitle)) {
            throw new \WHMCS\Exception("FeatureHighlight Entities are required to have a subtitle.");
        }
        if(empty($iconImage)) {
            throw new \WHMCS\Exception("FeatureHighlight Entities are required to have an icon image.");
        }
        if(empty($description)) {
            throw new \WHMCS\Exception("FeatureHighlight Entities are required to have a description.");
        }
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->headlineImage = $headlineImage;
        $this->iconImage = $iconImage;
        $this->description = $description;
        if(!is_null($btn1Link)) {
            $this->btn1Link = $btn1Link;
            $this->btn1Label = $btn1Label;
        }
        if(!is_null($btn2Link)) {
            $this->btn2Link = $btn2Link;
            $this->btn2Label = $btn2Label;
        }
        $this->assetHelper = \DI::make("asset");
        return $this;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getSubtitle()
    {
        return $this->subtitle;
    }
    public function getImage($imageName = NULL)
    {
        if(substr($imageName, 0, 4) == "http") {
            return $imageName;
        }
        return "images/whatsnew/" . $imageName;
    }
    public function getIcon()
    {
        return $this->getImage($this->iconImage);
    }
    public function getHeadlineImage()
    {
        return $this->getImage($this->headlineImage);
    }
    public function hasHeadlineImage()
    {
        return !is_null($this->headlineImage);
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function hasBtn1Link()
    {
        return !is_null($this->btn1Link);
    }
    public function getBtn1Link()
    {
        if(!$this->btn1Link) {
            return NULL;
        }
        return $this->btn1Link;
    }
    public function getBtn1Label()
    {
        return $this->btn1Label;
    }
    public function hasBtn2Link()
    {
        return !is_null($this->btn2Link);
    }
    public function getBtn2Link()
    {
        if(!$this->btn2Link) {
            return NULL;
        }
        return $this->btn2Link;
    }
    public function getBtn2Label()
    {
        return $this->btn2Label;
    }
    public function hideIconBackgroundImage() : \self
    {
        $this->iconBackgroundEnabled = false;
        return $this;
    }
    public function isIconBackgroundEnabled()
    {
        return $this->iconBackgroundEnabled;
    }
}

?>