<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Smarty;

class ErrorPage extends BodyContentWrapper
{
    public function __construct($data = "", $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
        $this->setSidebarName("home");
        $this->setFavicon("warning");
    }
    public function getBody() : \Psr\Http\Message\StreamInterface
    {
        if(\WHMCS\Session::get("adminid")) {
            return parent::getBody();
        }
        return (new \WHMCS\Admin\ApplicationSupport\View\Html\PopUp($this->getBodyContent()))->getBody();
    }
}

?>