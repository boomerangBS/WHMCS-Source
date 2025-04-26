<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User;

abstract class AbstractUser extends \WHMCS\Model\AbstractModel
{
    public abstract function isAllowedToAuthenticate();
    public static function findUuid($uuid)
    {
        if(!$uuid) {
            return NULL;
        }
        return static::where("uuid", "=", $uuid)->first();
    }
    public static function boot()
    {
        parent::boot();
        static::creating(function (AbstractUser $model) {
            if(!$model->uuid) {
                $uuid = \Ramsey\Uuid\Uuid::uuid4();
                $model->uuid = $uuid->toString();
            }
        });
        static::saving(function (AbstractUser $model) {
            if(!$model->uuid) {
                $uuid = \Ramsey\Uuid\Uuid::uuid4();
                $model->uuid = $uuid->toString();
            }
        });
    }
}

?>