<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ApplicationLink\Server;

interface ApplicationLinkServerInterface extends \OAuth2\Controller\TokenControllerInterface, \OAuth2\Controller\ResourceControllerInterface
{
    public function postAccessTokenResponseAction(\OAuth2\RequestInterface $request, \OAuth2\ResponseInterface $response);
}

?>