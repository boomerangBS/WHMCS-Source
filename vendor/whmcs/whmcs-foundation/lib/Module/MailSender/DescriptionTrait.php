<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\MailSender;

trait DescriptionTrait
{
    protected $displayName = "";
    public function getName()
    {
        return basename(str_replace("\\", "/", get_class($this)));
    }
    public function getDisplayName()
    {
        return $this->displayName;
    }
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
}

?>