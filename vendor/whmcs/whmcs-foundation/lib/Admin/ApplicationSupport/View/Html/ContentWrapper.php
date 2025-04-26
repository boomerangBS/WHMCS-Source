<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html;

class ContentWrapper extends \WHMCS\Http\Message\AbstractViewableResponse implements \WHMCS\View\HtmlPageInterface
{
    use \WHMCS\Admin\ApplicationSupport\View\Traits\BodyContentTrait;
    public function __construct($data = "", $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
        $this->setBodyContent($data);
    }
    protected function getOutputContent()
    {
        $html = $this->getFormattedBodyContent();
        return (new \WHMCS\Admin\ApplicationSupport\View\PreRenderProcessor())->process($html);
    }
    public function getFormattedHtmlHeadContent()
    {
        return "";
    }
    public function getFormattedHeaderContent()
    {
        return "";
    }
    public function getFormattedBodyContent()
    {
        return $this->getBodyContent();
    }
    public function getFormattedFooterContent()
    {
        return "";
    }
}

?>