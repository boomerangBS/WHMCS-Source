<?php

namespace WHMCS\CustomField;

class CustomFieldValue extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblcustomfieldsvalues";
    protected $columnMap = ["relatedId" => "relid"];
    protected $fillable = ["fieldid", "relid"];
    public function customField()
    {
        return $this->belongsTo("WHMCS\\CustomField", "fieldid", "id", "customField");
    }
    public function addon()
    {
        return $this->belongsTo("WHMCS\\Service\\Addon", "relid", "id", "addon");
    }
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "relid", "id", "client");
    }
    public function service()
    {
        return $this->belongsTo("WHMCS\\Service\\Service", "relid", "id", "service");
    }
    public function getValueAttribute($value)
    {
        if(strtolower($this->customField->fieldType) === "password") {
            $decryptedValue = $this->decrypt($value);
            if(0 < strlen($decryptedValue)) {
                return $decryptedValue;
            }
        }
        return $value;
    }
    public function setValueAttribute($value) : void
    {
        if(strtolower($this->customField->fieldType) === "password" && !(is_null($value) || $value === "")) {
            $this->attributes["value"] = $this->encrypt($value);
        } else {
            $this->attributes["value"] = $value;
        }
    }
}

?>