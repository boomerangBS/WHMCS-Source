<?php

namespace WHMCS\Exception;

class ServiceUnavailable extends \WHMCS\Exception
{
    public static function factory($identifier = NULL, $initiatingException) : \self
    {
        return new static($identifier, 0, $initiatingException);
    }
}

?>