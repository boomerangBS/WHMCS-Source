<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ClientArea\Login;

class LoginRouteProvider implements \WHMCS\Route\Contracts\ProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        $routes = ["/login" => ["attributes" => ["authorization" => function () {
            return new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization();
        }], ["method" => ["GET"], "name" => "login-index", "path" => "", "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "index"]], ["method" => ["POST"], "name" => "login-validate", "path" => "", "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "validateLogin"]], ["method" => ["GET"], "name" => "login-two-factor-challenge", "path" => "/challenge", "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "twoFactorChallenge"]], ["method" => ["GET", "POST"], "name" => "login-two-factor-challenge-verify", "path" => "/challenge/verify", "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "twoFactorChallengeVerify"]], ["method" => ["POST"], "name" => "login-two-factor-challenge-backup-verify", "path" => "/challenge/backup", "authorization" => function (\WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken();
        }, "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "twoFactorBackupCodeVerify"]], ["method" => ["GET"], "name" => "login-two-factor-challenge-backup-new", "path" => "/challenge/backup", "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "twoFactorBackupCodeNew"]], ["method" => ["POST"], "name" => "login-cart-login", "path" => "/cart", "authorization" => function (\WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken();
        }, "handle" => ["WHMCS\\ClientArea\\Login\\LoginController", "cartLogin"]]]];
        return $routes;
    }
    public function registerRoutes(\FastRoute\RouteCollector $routeCollector)
    {
        $this->addRouteGroups($routeCollector, $this->getRoutes());
    }
}

?>