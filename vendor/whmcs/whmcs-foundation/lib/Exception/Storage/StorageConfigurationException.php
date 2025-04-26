<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Exception\Storage;

class StorageConfigurationException extends StorageException
{
    private $fields = [];
    public function __construct(array $fields)
    {
        parent::__construct(join(" ", array_values($fields)));
        $this->fields = $fields;
    }
    public function getFields()
    {
        return $this->fields;
    }
}

?>