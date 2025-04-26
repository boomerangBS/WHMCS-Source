<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version740release1 extends IncrementalVersion
{
    protected $updateActions = ["updateNotificationEmailTemplate"];
    protected function updateNotificationEmailTemplate()
    {
        $originalTemplateHash = "c2f01c95f1ab8fd779aa019a0469ac52";
        $email = \WHMCS\Mail\Template::master()->whereName("Default Notification Message")->first();
        if($email && md5($email->message) === $originalTemplateHash) {
            $email->message = "<h2><a href=\"{\$notification_url}\">{\$notification_title}</a></h2>\n<div>{\$notification_message}</div>\n{foreach from=\$notification_attributes item=\$attribute}\n<div>\n<div>{\$attribute.label}: {if \$attribute.icon}<img src=\"{\$attribute.icon}\" alt=\"\" />{/if}{if \$attribute.style}<span class=\"{\$attribute.style}\">{/if}{if \$attribute.url}<a href=\"{\$attribute.url}\">{\$attribute.value}</a>\n{else}{\$attribute.value}{/if}{if \$attribute.style}</span>{/if}</div>\n</div>\n{/foreach}";
            $email->save();
        }
    }
}

?>