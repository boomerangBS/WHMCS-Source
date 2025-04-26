<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
trait BodyContentTrait
{
    protected $bodyContent = "";
    public function getBodyContent()
    {
        return $this->bodyContent;
    }
    public function setBodyContent($content)
    {
        $this->bodyContent = (string) $content;
        return $this;
    }
}

?>