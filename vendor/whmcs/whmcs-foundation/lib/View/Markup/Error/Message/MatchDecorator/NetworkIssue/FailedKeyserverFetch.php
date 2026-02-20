<?php

namespace WHMCS\View\Markup\Error\Message\MatchDecorator\NetworkIssue;

class FailedKeyserverFetch extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    const PATTERN_GENERIC_GET_URL = "/Failed to get certificate metadata from keyserver. Error:/";
    const PATTERN_CRL_GET_URL = "/Failed to get CRL package from keyserver. Error:/";
    public function getTitle()
    {
        return "Network Issue - Failed Communication with Update Server";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/1913/updating#common-errors";
    }
    protected function isKnown($data)
    {
        return preg_match(self::PATTERN_CRL_GET_URL, $data) || preg_match(self::PATTERN_GENERIC_GET_URL, $data);
    }
}

?>