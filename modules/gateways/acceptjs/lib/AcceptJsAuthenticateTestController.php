<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\AcceptJs;

class AcceptJsAuthenticateTestController extends \net\authorize\api\controller\AuthenticateTestController
{
    public function __construct(\net\authorize\api\contract\v1\AnetApiRequestType $request)
    {
        parent::__construct($request);
        $this->httpClient = new AcceptJsHttpClient();
    }
}

?>