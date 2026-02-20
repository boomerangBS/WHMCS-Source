<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class GenericErrorResponse extends AbstractErrorResponse
{
    public $error;
    public $error_description;
    public function error()
    {
        return $this->error;
    }
    public function message()
    {
        return $this->error_description;
    }
    public static function factory($json) : \self
    {
        $r = parent::factory($json);
        if(is_null($r->error) || is_null($r->error_description)) {
            return NULL;
        }
        return $r;
    }
    public function __toString()
    {
        return sprintf("(%s) %s", $this->error(), $this->message());
    }
}

?>