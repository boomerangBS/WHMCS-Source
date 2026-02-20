<?php

namespace WHMCS\Module\Security\Totp\Generator;

class LocalQrGenerator extends AbstractGenerator
{
    public static function hasDependenciesMet()
    {
        return \WHMCS\Environment\PHP::isFunctionAvailable("iconv") && class_exists("\\XMLWriter") && class_exists("\\BaconQrCode\\Writer");
    }
    public function formatHtmlFromAuthString($authString)
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(new \BaconQrCode\Renderer\RendererStyle\RendererStyle($this->size()), new \BaconQrCode\Renderer\Image\SvgImageBackEnd());
        $writer = new \BaconQrCode\Writer($renderer);
        $code = $writer->writeString(urldecode($authString));
        $svg = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", $code);
        return $svg;
    }
}

?>