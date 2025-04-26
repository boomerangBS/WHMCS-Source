<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator;

trait GenericMatchDecorationTrait
{
    public function toHtml()
    {
        return $this->toGenericHtml(implode("\n", $this->getParsedMessageList()));
    }
    public function toPlain()
    {
        return $this->toGenericPlain(implode("\n", $this->getParsedMessageList()));
    }
}

?>