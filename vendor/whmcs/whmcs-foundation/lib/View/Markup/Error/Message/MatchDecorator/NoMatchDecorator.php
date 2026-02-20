<?php

namespace WHMCS\View\Markup\Error\Message\MatchDecorator;

class NoMatchDecorator extends AbstractMatchDecorator
{
    use GenericMatchDecorationTrait;
    public function getTitle()
    {
        return "Error";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/1913/updating#common-errors";
    }
    protected function isKnown($data)
    {
        return true;
    }
}

?>