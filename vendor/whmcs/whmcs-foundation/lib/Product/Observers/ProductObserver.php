<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\Observers;

class ProductObserver
{
    public function created(\WHMCS\Product\Product $product)
    {
        $product->assignMatchingMarketConnectAddons(\WHMCS\MarketConnect\Service::getAutoAssignableAddons());
        logAdminActivity("Product Created - '" . $product->name . "' - Product ID: " . $product->id);
    }
    public function creating(\WHMCS\Product\Product $product)
    {
        if(!isset($product->welcomeEmailTemplateId)) {
            $emailTemplate = false;
            if(isset(\WHMCS\Product\Product::DEFAULT_EMAIL_TEMPLATES[$product->type])) {
                $emailTemplate = \WHMCS\Mail\Template::master()->where("name", \WHMCS\Product\Product::DEFAULT_EMAIL_TEMPLATES[$product->type])->first();
            }
            if($emailTemplate) {
                $product->welcomeEmailTemplateId = $emailTemplate->id;
            }
        }
    }
    public function deleted(\WHMCS\Product\Product $product)
    {
        if(\WHMCS\Config\Setting::getValue("EnableTranslations")) {
            \WHMCS\Language\DynamicTranslation::whereIn("related_type", ["product.{id}.tagline", "product.{id}.short_description", "product.{id}.description", "product.{id}.name"])->where("related_id", "=", $product->id)->delete();
        }
        $product->slugs()->delete();
        $usageItems = \WHMCS\UsageBilling\Product\UsageItem::ofRelated($product)->get();
        foreach ($usageItems as $usageItem) {
            $usageItem->delete();
        }
    }
    public function saved(\WHMCS\Product\Product $product)
    {
        if(\WHMCS\Config\Setting::getValue("EnableTranslations")) {
            $translation = \WHMCS\Language\DynamicTranslation::firstOrNew(["related_type" => "product.{id}.tagline", "related_id" => $product->id, "language" => \WHMCS\Config\Setting::getValue("Language"), "input_type" => "text"]);
            $translation->translation = $product->getRawAttribute("tagline") ?: "";
            $translation->save();
            $translation = \WHMCS\Language\DynamicTranslation::firstOrNew(["related_type" => "product.{id}.short_description", "related_id" => $product->id, "language" => \WHMCS\Config\Setting::getValue("Language"), "input_type" => "textarea"]);
            $translation->translation = $product->getRawAttribute("short_description") ?: "";
            $translation->save();
            $translation = \WHMCS\Language\DynamicTranslation::firstOrNew(["related_type" => "product.{id}.description", "related_id" => $product->id, "language" => \WHMCS\Config\Setting::getValue("Language"), "input_type" => "textarea"]);
            $translation->translation = $product->getRawAttribute("description") ?: "";
            $translation->save();
            $translation = \WHMCS\Language\DynamicTranslation::firstOrNew(["related_type" => "product.{id}.name", "related_id" => $product->id, "language" => \WHMCS\Config\Setting::getValue("Language"), "input_type" => "text"]);
            $translation->translation = $product->getRawAttribute("name") ?: "";
            $translation->save();
        }
    }
}

?>