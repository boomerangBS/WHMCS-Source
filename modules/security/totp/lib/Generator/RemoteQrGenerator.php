<?php

namespace WHMCS\Module\Security\Totp\Generator;

class RemoteQrGenerator extends AbstractGenerator
{
    public static function hasDependenciesMet()
    {
        return true;
    }
    public function formatHtmlFromAuthString($authString)
    {
        $url = $this->generateUrl($authString);
        return "<img src=\"" . $url . "\" style=\"border: 1px solid #ccc;border-radius: 4px;margin:15px 0;\" alt=\"barcode\">";
    }
    private function generateUrl($content)
    {
        return sprintf("https://api.qrserver.com/v1/create-qr-code/?size=%dx%1\$d&data=%s", $this->size(), $content);
    }
}

?>