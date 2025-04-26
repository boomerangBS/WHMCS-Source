<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Apps\Category;

class Model
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function getSlug()
    {
        return $this->data["slug"];
    }
    public function getDisplayName()
    {
        return $this->data["name"];
    }
    public function getTagline()
    {
        return $this->data["tagline"];
    }
    public function getModuleType()
    {
        return $this->data["moduleType"];
    }
    public function getFeatured()
    {
        return $this->data["featured"] ?? [];
    }
    public function getExclusions() : array
    {
        return $this->data["exclusions"] ?? [];
    }
    public function getAdditions() : array
    {
        return $this->data["additions"] ?? [];
    }
    public function getIcon()
    {
        return isset($this->data["icon"]) ? $this->data["icon"] : "fas fa-star";
    }
    public function getHeroes() : \WHMCS\Apps\Hero\Collection
    {
        $heroes = [];
        if(isset($this->data["heros_v2"]) && is_array($this->data["heros_v2"])) {
            $heroes = $this->data["heros_v2"];
        }
        return new \WHMCS\Apps\Hero\Collection($heroes);
    }
    public function includeInHomeFeatured()
    {
        return isset($this->data["includeInHomeFeatured"]) && $this->data["includeInHomeFeatured"];
    }
    public function getHomeFeaturedNumApps()
    {
        return isset($this->data["homeNumFeaturedApps"]) && is_numeric($this->data["homeNumFeaturedApps"]) ? $this->data["homeNumFeaturedApps"] : 4;
    }
    public function getFeaturedAppKeys()
    {
        $featured = $this->getFeatured();
        $country = strtolower(\WHMCS\Config\Setting::getValue("DefaultCountry"));
        return array_key_exists($country, $featured) ? $featured[$country] : $featured["default"];
    }
    public function getFeaturedApps($apps)
    {
        $featuredApps = $this->getFeaturedAppKeys();
        $appsToReturn = [];
        foreach ($this->getFeaturedAppKeys() as $appKey) {
            $app = $apps->get($appKey);
            if($app) {
                $appsToReturn[] = $app;
            }
        }
        return $appsToReturn;
    }
    public function getFeaturedAppsForHome($apps)
    {
        $featuredApps = $this->getFeaturedApps($apps);
        return array_slice($featuredApps, 0, $this->getHomeFeaturedNumApps());
    }
    protected function getAllApps($apps)
    {
        $appsToReturn = [];
        foreach ($apps->all() as $app) {
            if(($app->getModuleType() == $this->getModuleType() && !$app->getCategory() || in_array($app->getKey(), $this->getAdditions()) || $app->getCategory() == $this->getSlug()) && !in_array($app->getKey(), $this->getExclusions())) {
                $appsToReturn[] = $app;
            }
        }
        return $appsToReturn;
    }
    public function getNonFeaturedApps($apps)
    {
        $apps = $this->getAllApps($apps);
        $featuredAppKeys = $this->getFeaturedAppKeys();
        foreach ($apps as $key => $app) {
            if(in_array($app->getKey(), $featuredAppKeys)) {
                unset($apps[$key]);
            }
        }
        return $apps;
    }
    public function getHero(\WHMCS\Apps\App\Collection $apps) : \WHMCS\Apps\Hero\Model
    {
        foreach ($this->getHeroes()->get() as $hero) {
            $targetAppKey = $hero->getTargetAppKey();
            if($apps->exists($targetAppKey) && !$apps->get($targetAppKey)->isActive()) {
                return $hero;
            }
        }
        return NULL;
    }
}

?>