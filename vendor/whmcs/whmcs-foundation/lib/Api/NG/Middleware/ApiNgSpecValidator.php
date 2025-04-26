<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Middleware;

class ApiNgSpecValidator implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\AssumingMiddlewareTrait;
    protected function isErrorDisplayAllowed(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\ServerRequest
    {
        return (bool) \App::getApplicationConfig()->display_errors;
    }
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $apiVersion = trim($request->getAttribute(\WHMCS\Route\Middleware\RoutableApiRequestUri::ATTRIBUTE_API_NG_VERSION, "") ?? "");
        if(!preg_match("/^v\\d+\$/", $apiVersion)) {
            return new \WHMCS\Http\Message\JsonResponse([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
        $specFile = ROOTDIR . "/resources/api/" . $apiVersion . "/whmcs.yaml";
        if(!file_exists($specFile)) {
            return new \WHMCS\Http\Message\JsonResponse(["message" => "Unsupported API version"], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE);
        }
        $validatorBuilder = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder())->fromYamlFile($specFile);
        try {
            $apiOperation = $validatorBuilder->getServerRequestValidator()->validate($request);
        } catch (\League\OpenAPIValidation\PSR7\Exception\ValidationFailed $e) {
            if($this->isErrorDisplayAllowed($request)) {
                throw $e;
            }
            if($e instanceof \League\OpenAPIValidation\PSR7\Exception\NoOperation) {
                return new \WHMCS\Http\Message\JsonResponse(["message" => "Unsupported operation or request method"], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE);
            }
            return new \WHMCS\Http\Message\JsonResponse(["message" => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }
        $response = $delegate->process($request);
        try {
            $validatorBuilder->getResponseValidator()->validate($apiOperation, $response);
        } catch (\League\OpenAPIValidation\PSR7\Exception\ValidationFailed $e) {
            if($this->isErrorDisplayAllowed($request)) {
                throw $e;
            }
            return new \WHMCS\Http\Message\JsonResponse(["message" => "Internal error, please try again later"], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }
}

?>