<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Apps\Meta\Schema;

class AbstractVersion
{
    public $metaData = [];
    public function __construct(array $metaData)
    {
        $this->metaData = $metaData;
    }
    protected function meta($key)
    {
        $parts = explode(".", $key);
        $response = $this->metaData;
        foreach ($parts as $part) {
            if(isset($response[$part])) {
                $response = $response[$part];
            } else {
                return NULL;
            }
        }
        return $response;
    }
}

?>