<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2;

abstract class AbstractApiEntityDecorator implements ApiEntityDecoratorInterface
{
    const DATA_KEYS = [];
    protected function formatToArray($entity) : array
    {
        if(!$entity instanceof \JsonSerializable) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("The default implementation can only format objects that use the JsonSerializable interface.");
        }
        return $entity->jsonSerialize();
    }
    public function decorate($entity) : array
    {
        if(!is_a($entity, static::getEntityClass())) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("The entity is not an instance of " . static::getEntityClass());
        }
        $data = $this->formatToArray($entity);
        if(static::DATA_KEYS) {
            $data = array_intersect_key($data, array_flip(static::DATA_KEYS));
        }
        return $data;
    }
}

?>