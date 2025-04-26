<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Model;

class Observer
{
    public function creating(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->enforceUniqueConstraint($model);
    }
    public function updating(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->enforceUniqueConstraint($model);
        $this->enforceGuardedForUpdateProperties($model);
    }
    protected function enforceUniqueConstraint(\Illuminate\Database\Eloquent\Model $model)
    {
        $class = get_class($model);
        foreach ($model->unique as $property) {
            if($model->isDirty($property)) {
                $existingModelQuery = $class::where($property, "=", $model->{$property});
                if($model->exists) {
                    $existingModelQuery->where("id", "!=", $model->id);
                }
                if(0 < $existingModelQuery->count()) {
                    throw new \WHMCS\Exception\Model\UniqueConstraint("A \"" . $class . "\" record with \"" . $property . "\" value \"" . $model->{$property} . "\" already exists.");
                }
            }
        }
    }
    protected function enforceGuardedForUpdateProperties(\Illuminate\Database\Eloquent\Model $model)
    {
        $class = get_class($model);
        foreach ($model->guardedForUpdate as $property) {
            if($model->isDirty($property)) {
                throw new \WHMCS\Exception\Model\GuardedForUpdate("The \"" . $class . "\" record \"" . $property . "\" property is guarded against updates.");
            }
        }
    }
}

?>