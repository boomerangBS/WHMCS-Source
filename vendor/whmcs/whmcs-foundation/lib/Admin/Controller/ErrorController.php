<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Controller;

class ErrorController
{
    use \WHMCS\Application\Support\Controller\DelegationTrait;
    public function loginRequired(\WHMCS\Http\Message\ServerRequest $request)
    {
        $msg = "Admin Login Required";
        if($request->expectsJsonResponse()) {
            $response = new \WHMCS\Http\Message\JsonResponse(["status" => "error", "errorMessage" => $msg], 403);
        } else {
            $response = $this->redirectTo("admin-login", $request);
        }
        return $response;
    }
}

?>