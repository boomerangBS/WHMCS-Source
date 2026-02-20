<?php

namespace WHMCS\View\Template;

class OrderFormValues extends AbstractConfigValues
{
    protected function defaultPathMap() : array
    {
        return ["css" => "/css", "img" => "/img", "js" => "/js"];
    }
    protected function calculateValues() : array
    {
        return ["orderform" => $this->defaultValues()];
    }
}

?>