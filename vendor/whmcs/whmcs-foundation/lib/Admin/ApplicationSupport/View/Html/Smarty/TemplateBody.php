<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Smarty;

class TemplateBody extends BodyContentWrapper
{
    public function __construct($bodyTemplateName)
    {
        parent::__construct();
        $this->setTemplateName($bodyTemplateName);
    }
    public function getBodyContent()
    {
        if(!$this->bodyContent) {
            $this->bodyContent = "";
            $smarty = $this->getTemplateEngine();
            if($this->getTemplateName()) {
                $this->bodyContent = $smarty->fetch($this->getTemplateDirectory() . "/" . $this->getTemplateName() . ".tpl");
            }
        }
        return $this->bodyContent;
    }
}

?>