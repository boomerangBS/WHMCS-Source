<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http\Message;

class JsonFormResponse extends JsonResponse
{
    public static function createWithSuccess($data = NULL)
    {
        return new static(["data" => $data]);
    }
    public static function createWithErrors(array $data)
    {
        return new static(["fields" => $data], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
    }
}

?>