<?php

namespace WHMCS\Service\Adapters;

class SitejetAdapter extends AbstractServiceAdapter
{
    use SitejetProductAwareTrait;
    protected function getProduct() : \WHMCS\Product\Product
    {
        return $this->service->product;
    }
    public function offersSitejetNatively($allowInactive)
    {
        if(!$this->service->product || $this->service->product->type !== "hostingaccount" || !$this->service->product->module || !$this->service->serverModel) {
            return false;
        }
        if(!$allowInactive && $this->service->status !== \WHMCS\Service\Service::STATUS_ACTIVE) {
            return false;
        }
        $servicePackage = $this->service->product->moduleConfigOption1;
        if(!$servicePackage) {
            return false;
        }
        return \WHMCS\Product\Server\Adapters\SitejetServerAdapter::factory($this->service->serverModel)->hasSitejetForPackage($servicePackage);
    }
    protected function isSitejetAddon(\WHMCS\Service\Addon $addon = false, $allowInactive) : \WHMCS\Service\Addon
    {
        if($addon->serviceId !== $this->service->id) {
            return false;
        }
        $sitejetAddonProductKey = $this->getSitejetAddonProductKey();
        if(is_null($sitejetAddonProductKey)) {
            return false;
        }
        if(!$allowInactive && $addon->status !== \WHMCS\Service\Service::STATUS_ACTIVE) {
            return false;
        }
        if($addon->provisioningType !== \WHMCS\Product\Addon::PROVISIONING_TYPE_FEATURE) {
            return false;
        }
        if(!$addon->productAddon || $addon->productAddon->productKey !== $sitejetAddonProductKey) {
            return false;
        }
        return true;
    }
    public function offersSitejetViaAddon(\WHMCS\Service\Addon $addon = NULL, $allowInactive = false)
    {
        if($addon) {
            return $this->isSitejetAddon($addon, $allowInactive);
        }
        $activeSitejetAddon = $this->service->addons->first(function (\WHMCS\Service\Addon $serviceAddon) use($allowInactive) {
            return $this->isSitejetAddon($serviceAddon, $allowInactive);
        });
        return !is_null($activeSitejetAddon);
    }
    public function isSitejetActive()
    {
        if($this->offersSitejetNatively()) {
            return true;
        }
        return $this->offersSitejetViaAddon();
    }
    protected function assertSitejetActive() : void
    {
        if(!$this->isSitejetActive()) {
            throw new \WHMCS\Exception\Module\NotServicable("Sitejet Builder is not active for this service.");
        }
    }
    public function getAvailableSitejetProductUpgrades() : \Illuminate\Support\Collection
    {
        $availableProductUpgrades = [];
        $productUpgrades = $this->getProduct()->upgradeProducts;
        foreach ($productUpgrades as $upgradeProduct) {
            $siteJetUpgradeAvailable = \WHMCS\Product\Server\Adapters\SitejetServerAdapter::factory($this->service->serverModel)->hasSitejetForPackage($upgradeProduct->moduleConfigOption1);
            if($siteJetUpgradeAvailable) {
                $availableProductUpgrades[] = $upgradeProduct;
            }
        }
        return collect($availableProductUpgrades);
    }
    public function publishSitejet() : array
    {
        $this->assertSitejetActive();
        $result = $this->moduleCall("StartSitejetPublish");
        if(!isset($result["publish_metadata"])) {
            throw new \WHMCS\Exception\Module\NotServicable("Invalid (null) response from Sitejet publish operation");
        }
        \WHMCS\Utility\Sitejet\SitejetStats::logEvent($this->service, \WHMCS\Utility\Sitejet\SitejetStats::NAME_PUBLISH);
        return $result["publish_metadata"];
    }
    public function getSitejetPublishProgress($publishMetadata) : array
    {
        $this->assertSitejetActive();
        $result = $this->moduleCall("GetSitejetPublishProgress", ["publish_metadata" => $publishMetadata]);
        return ["progress" => $result["progress"], "completed" => $result["completed"], "success" => $result["success"]];
    }
    public function getSitejetPreviewImageUrl(\WHMCS\License $license = false, $forceRefresh) : \WHMCS\License
    {
        $screenshotApi = \WHMCS\Utility\WebsiteScreenshotApiHandler::createFromLicense($license);
        $cacheTtl = $forceRefresh ? \WHMCS\Utility\WebsiteScreenshotApiHandler::CACHE_TTL_FORCE_NEW : \WHMCS\Utility\WebsiteScreenshotApiHandler::CACHE_TTL_RETURN_CACHED;
        $imageUrl = $screenshotApi->getWebsiteScreenshotUrl("http://" . $this->service->domain, $cacheTtl);
        return $imageUrl;
    }
}

?>