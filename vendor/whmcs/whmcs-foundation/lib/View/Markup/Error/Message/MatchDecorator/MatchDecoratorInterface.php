<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator;

interface MatchDecoratorInterface extends \WHMCS\View\Markup\Error\Message\DecoratorInterface, \WHMCS\View\Markup\Error\ErrorLevelInterface
{
    public function wrap(\Iterator $data);
    public function getData();
    public function hasMatch();
}

?>