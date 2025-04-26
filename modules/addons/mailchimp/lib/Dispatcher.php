<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Addon\Mailchimp;

class Dispatcher
{
    public function dispatch($action, $parameters)
    {
        if(!$action) {
            $action = "index";
        }
        $controller = new Controller();
        if(is_callable([$controller, $action])) {
            $response = $controller->{$action}($parameters);
            if(isset($response["ajax"]) && $response["ajax"]) {
                return $response;
            }
            if(isset($response["action"])) {
                $action = $response["action"];
            } else {
                $response["action"] = $action;
            }
            return $this->renderView($action, $response);
        }
        return "<p>Invalid action requested. Please go back and try again.</p>";
    }
    public function renderView($action, $parameters)
    {
        $templateEngine = \DI::make("View\\Engine\\Php\\Admin");
        $spaceDir = ROOTDIR . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "addons" . DIRECTORY_SEPARATOR . "mailchimp" . DIRECTORY_SEPARATOR . "views";
        $templateEngine->setDirectory($spaceDir);
        $templateEngine->addData($parameters);
        $templateEngine->addData(["content" => $templateEngine->render($action)]);
        return $templateEngine->render("layout");
    }
}

?>