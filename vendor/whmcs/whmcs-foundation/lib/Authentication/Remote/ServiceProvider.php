<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication\Remote;

class ServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider implements \WHMCS\Route\Contracts\ProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function register()
    {
    }
    public function registerRoutes(\FastRoute\RouteCollector $routeCollector)
    {
        $this->addRouteGroups($routeCollector, $this->getClientAreaManagementRoutes());
        $this->addRouteGroups($routeCollector, $this->getProviderRoutes());
    }
    private function getClientAreaManagementRoutes()
    {
        return ["/auth/manage/client" => [["name" => "auth-manage-client-delete", "method" => "POST", "path" => "/delete/[{authnid:\\d+}]", "handle" => ["WHMCS\\Authentication\\Remote\\Management\\Client\\Controller", "delete"]], ["name" => "auth-manage-client-links", "method" => "GET", "path" => "/links", "handle" => ["WHMCS\\Authentication\\Remote\\Management\\Client\\Controller", "getLinks"]]]];
    }
    private function getProviderRoutes()
    {
        return ["/auth/provider/google_signin" => [["name" => "auth-provider-google_signin-finalize", "method" => "POST", "path" => "/finalize", "handle" => ["WHMCS\\Authentication\\Remote\\Providers\\Google\\GoogleSignin", "finalizeSignin"]]], "/auth/provider/facebook_signin" => [["name" => "auth-provider-facebook_signin-finalize", "method" => "POST", "path" => "/finalize", "handle" => ["WHMCS\\Authentication\\Remote\\Providers\\Facebook\\FacebookSignin", "finalizeSignin"]]], "/auth/provider/twitter_oauth" => [["name" => "auth-provider-twitter_oauth-authorize", "method" => "POST", "path" => "/authorize", "handle" => ["WHMCS\\Authentication\\Remote\\Providers\\Twitter\\TwitterOauth", "authorizeSignin"]], ["name" => "auth-provider-twitter_oauth-callback", "method" => "GET", "path" => "/callback", "handle" => ["WHMCS\\Authentication\\Remote\\Providers\\Twitter\\TwitterOauth", "signinCallback"]]]];
    }
}

?>