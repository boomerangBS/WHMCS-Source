<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Menu\Factory;

class WhmcsExtension implements \Knp\Menu\Factory\ExtensionInterface
{
    public function buildOptions($options) : array
    {
        return array_merge(["uri" => NULL, "badge" => NULL, "order" => NULL, "icon" => NULL, "headingHtml" => NULL, "bodyHtml" => NULL, "footerHtml" => NULL, "disabled" => false], $options);
    }
    public function buildItem(\Knp\Menu\ItemInterface $item, array $options) : void
    {
        $item->setUri($options["uri"])->setBadge($options["badge"])->setOrder($options["order"])->setIcon($options["icon"])->setHeadingHtml($options["headingHtml"])->setBodyHtml($options["bodyHtml"])->setFooterHtml($options["footerHtml"]);
        if($options["disabled"]) {
            $item->disable();
        }
    }
}

?>