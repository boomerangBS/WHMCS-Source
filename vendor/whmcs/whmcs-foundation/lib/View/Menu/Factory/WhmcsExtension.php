<?php

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