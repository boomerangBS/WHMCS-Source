<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\ApplicationSupport\Route\Middleware;

class ActionFilter implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\AssumingMiddlewareTrait;
    protected $apiFunctionsRestrictedToLocalApi = ["setconfigurationvalue"];
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $action = $request->getAction();
        $action = preg_replace("/[^0-9a-z]/i", "", strtolower($action));
        $action = $this->resolveLegacyAction($action);
        $request = $request->withAttribute("action", $action);
        if($this->isRestrictedToLocalApi($request)) {
            throw new \Exception("API Command Restricted to Internal API");
        }
        return $delegate->process($request);
    }
    public function isRestrictedToLocalApi(\WHMCS\Api\ApplicationSupport\Http\ServerRequest $request)
    {
        return in_array($request->getAction(), $this->apiFunctionsRestrictedToLocalApi);
    }
    protected function resolveLegacyAction($action = "")
    {
        switch ($action) {
            case "getclientsdata":
            case "getclientsdatabyemail":
                $action = "getclientsdetails";
                break;
            default:
                return $action;
        }
    }
}

?>