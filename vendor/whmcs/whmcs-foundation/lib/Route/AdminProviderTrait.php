<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route;

trait AdminProviderTrait
{
    use ProviderTrait;
    protected function enforceAdminAuthentication(array $routeCollection)
    {
        $noAuthRoutes = ["dev-test", "admin-login"];
        foreach ($routeCollection as $routeKey => &$route) {
            if($routeKey === "attributes" && isset($route["authentication"]) && in_array($route["authentication"], ["admin", "adminConfirmation"])) {
                return $routeCollection;
            }
            if(!isset($route["authentication"]) && (!isset($route["name"]) || !in_array($route["name"], $noAuthRoutes))) {
                $route["authentication"] = "admin";
            }
        }
    }
    public function mutateAdminRoutesForCustomDirectory(array $adminRoutes = [])
    {
        $adminBasePath = \WHMCS\Admin\AdminServiceProvider::getAdminRouteBase();
        $mutatedRoutes = [];
        foreach ($adminRoutes as $key => $value) {
            if(is_array($value)) {
                $value = $this->enforceAdminAuthentication($value);
            }
            if(strpos($key, "/admin") === 0) {
                $mutatedKey = $adminBasePath . substr($key, 6);
                $mutatedRoutes[$mutatedKey] = $value;
            } else {
                $mutatedRoutes[$key] = $value;
            }
        }
        return $mutatedRoutes;
    }
    public function registerRoutes(\FastRoute\RouteCollector $routeCollector)
    {
        $this->addRouteGroups($routeCollector, $this->mutateAdminRoutesForCustomDirectory($this->getRoutes()));
    }
}

?>