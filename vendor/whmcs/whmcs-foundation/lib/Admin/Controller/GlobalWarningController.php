<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Controller;

class GlobalWarningController
{
    public function dismiss(\WHMCS\Http\Message\ServerRequest $request)
    {
        $alertToDismiss = $request->get("alert");
        (new \WHMCS\Admin\ApplicationSupport\View\Html\Helper\GlobalWarning())->updateDismissalTracker($alertToDismiss);
        return new \WHMCS\Http\Message\JsonResponse(["status" => "success"]);
    }
}

?>