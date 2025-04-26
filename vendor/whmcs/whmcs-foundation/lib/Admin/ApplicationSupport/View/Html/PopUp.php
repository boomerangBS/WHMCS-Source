<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html;

class PopUp extends AbstractNoEngine
{
    public function __construct($data = "", $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
        $this->setBodyContent($data);
    }
    public function getFormattedHeaderContent()
    {
        $html = "<body class=\"popup-body\">\n    <div class=\"popup-content-area\">\n        <table width=\"100%\" bgcolor=\"#ffffff\" cellpadding=\"15\"><tr><td>\n\n        <h2>" . $this->getTitle() . "</h2>\n";
        return $html;
    }
    public function getFormattedFooterContent()
    {
        $html = "        \n        </td></tr></table>\n    </div>\n</body>\n</html>";
        return $html;
    }
}

?>