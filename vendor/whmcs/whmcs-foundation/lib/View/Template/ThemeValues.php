<?php

namespace WHMCS\View\Template;

class ThemeValues extends AbstractConfigValues
{
    protected function defaultPathMap() : array
    {
        return ["css" => "/css", "fonts" => "/fonts", "img" => "/img", "js" => "/js"];
    }
    protected function calculateValues() : array
    {
        $theme = $this->getTemplate();
        return ["template" => $theme->getName(), "webroot" => $this->getWebRoot(), "theme" => $this->defaultValues()];
    }
}

?>