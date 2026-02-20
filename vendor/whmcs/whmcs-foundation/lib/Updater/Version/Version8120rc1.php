<?php

namespace WHMCS\Updater\Version;

class Version8120rc1 extends IncrementalVersion
{
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove = array_merge(["//admin/images/whatsnew/modal-captcha.png", "//admin/images/whatsnew/modal-invoice.png", "//admin/images/whatsnew/modal-localisation.png", "//admin/images/whatsnew/modal-paypal.png", "//admin/images/whatsnew/modal-php.png", "//admin/images/whatsnew/modal-ticket.png"], self::ticketScheduledActionFiles(), self::ticketScheduledActionTemplates());
    }
    public function getFeatureHighlights() : array
    {
        return [(new \WHMCS\Notification\FeatureHighlight("Scheduled Actions for Support Tickets", "Set up automated actions to streamline your customer service operations.", NULL, "ticket-scheduled-actions.png", "You can easily schedule replies, changes to ticket priority level, reassignment, and other actions for individual tickets.", "https://go.whmcs.com/2493/scheduled-actions", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("Pinning for Support Tickets", "Pin tickets to the top of the Admin Area support ticket list.", NULL, "ticket-pinning.png", "Pinning lets you increase visibility and easily access important or high-sensitivity tickets.", "https://go.whmcs.com/2501/pin-to-top", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("Admin Invites", "Invite new staff members to start using the WHMCS Admin Area.", NULL, "admin-invites.png", "New admins can securely choose their own login credentials when they accept your invitation.", "https://go.whmcs.com/2489/invite-admin", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("MarketConnect SSL Promotional Logic", "New and improved promotional content for selling SSL certificates with MarketConnect.", NULL, "marketconnect-promo-logic.png", "The Client Area landing page and promotions now display the most up-to-date information on SSL certificates.", "https://go.whmcs.com/2497/", "Learn More"))->hideIconBackgroundImage(), (new \WHMCS\Notification\FeatureHighlight("MarketConnect SSL Promotional Logic", "MarketConnect SSL certificate promotions now display based on new and improved logic.", NULL, "marketconnect-ssl-upsells.png", "Our new logic for upselling certificates, based on DigiCert® best practices, will help you increase conversions.", "https://go.whmcs.com/2497/", "Learn More"))->hideIconBackgroundImage()];
    }
    public static function ticketScheduledActionFiles() : array
    {
        return ["//vendor/whmcs/whmcs-foundation/lib/Support/Actions/"];
    }
    public static function ticketScheduledActionTemplates() : array
    {
        return ["//admin/templates/blend/scheduledactionspanel.tpl", "//admin/templates/blend/scheduledactionslist.tpl", "//admin/templates/blend/ticketactionstab.tpl"];
    }
}

?>