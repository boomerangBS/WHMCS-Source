<?php

namespace WHMCS\Utility\Sitejet;

class SitejetStats
{
    private $serviceOrAddon;
    const SERVICE_DATA_SCOPE = "sitejet";
    const NAME_SSO = "sso";
    const NAME_PUBLISH = "publish";
    const NAME_SERVICE_ORDER = "service_order";
    const NAME_ADDON_ORDER = "addon_order";
    const NAME_ADDON_BUNDLE_ORDER = "addon_bundle_order";
    const NAME_SERVICE_UPGRADE = "upgrade";
    const VALUE_TTL_DAYS = NULL;
    public function __construct($serviceOrAddon)
    {
        if(!$serviceOrAddon instanceof \WHMCS\Service\Service && !$serviceOrAddon instanceof \WHMCS\Service\Addon) {
            throw new \WHMCS\Exception("Invalid parent entity for Sitejet stats");
        }
        $this->serviceOrAddon = $serviceOrAddon;
    }
    protected function add($name = NULL, string $value = NULL, string $actor = NULL, $expiresAt) : void
    {
        $serviceData = new \WHMCS\Service\ServiceData();
        if(is_null($expiresAt) && isset(self::VALUE_TTL_DAYS[$name])) {
            $expiresAt = \WHMCS\Carbon::now()->addDays(self::VALUE_TTL_DAYS[$name]);
        }
        if(is_null($actor)) {
            if(defined("ADMINAREA")) {
                $actor = \WHMCS\Service\ServiceData::ACTOR_ADMIN;
            } elseif(defined("CLIENTAREA")) {
                $actor = \WHMCS\Service\ServiceData::ACTOR_CLIENT;
            } elseif(defined("APICALL")) {
                $actor = \WHMCS\Service\ServiceData::ACTOR_API;
            } else {
                $actor = \WHMCS\Service\ServiceData::ACTOR_OTHER;
            }
        }
        $serviceData->setServiceOrAddon($this->serviceOrAddon)->setActor($actor)->setScope(self::SERVICE_DATA_SCOPE)->setName($name)->setValue($value)->setExpiresAt($expiresAt)->save();
    }
    protected function getControlPanel()
    {
        if($this->serviceOrAddon instanceof \WHMCS\Service\Service) {
            return $this->serviceOrAddon->product->module;
        }
        if($this->serviceOrAddon instanceof \WHMCS\Service\Addon) {
            if($this->serviceOrAddon->service) {
                return $this->serviceOrAddon->service->product->module;
            }
            if($this->serviceOrAddon->productAddon) {
                return $this->serviceOrAddon->productAddon->module;
            }
        }
    }
    public static function logEvent($serviceOrAddon, string $statsName)
    {
        try {
            $self = new static($serviceOrAddon);
            $self->add($statsName, $self->getControlPanel());
        } catch (\Throwable $e) {
        }
    }
}

?>