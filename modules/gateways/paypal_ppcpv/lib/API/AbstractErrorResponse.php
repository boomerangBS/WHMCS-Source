<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
abstract class AbstractErrorResponse extends AbstractResponse
{
    public static function factory(string $json)
    {
        $response = new static();
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($json);
        if($decoded === false) {
            return NULL;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $response);
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        $r = static::factory($response->body);
        if(!is_null($r)) {
            return $r;
        }
        return new InternalErrorResponse($response);
    }
}

?>