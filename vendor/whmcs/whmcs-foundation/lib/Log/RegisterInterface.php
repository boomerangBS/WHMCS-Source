<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Log;

interface RegisterInterface
{
    public function __toString();
    public function toArray();
    public function toJson();
    public function getName();
    public function setName($name);
    public function getNamespace();
    public function setNamespace($key);
    public function getNamespaceId();
    public function setNamespaceId($id);
    public function setValue($value);
    public function getValue();
    public function write($value);
    public function latestByNamespaces(array $namespaces, $id);
}

?>