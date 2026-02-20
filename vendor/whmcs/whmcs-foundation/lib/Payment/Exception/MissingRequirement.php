<?php

namespace WHMCS\Payment\Exception;

class MissingRequirement extends \LogicException
{
    public static function ofImplementor($requirement, string $implementor) : \self
    {
        return new static(sprintf("%s missing required %s", $implementor, $requirement));
    }
}

?>