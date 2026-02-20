<?php

namespace WHMCS\Apps\App;

class Collection
{
    protected $apps = [];
    public function __construct(array $moduleSlugs = [])
    {
        $this->initialiseApps($moduleSlugs);
    }
    public static function getAppBySlug($moduleSlug) : Model
    {
        return (new self([$moduleSlug]))->get($moduleSlug);
    }
    protected function initialiseApps(array $moduleSlugs = [])
    {
        $moduleClasses = [];
        $moduleNames = [];
        foreach ($moduleSlugs as $moduleSlug) {
            $slugParts = explode(".", $moduleSlug, 2);
            $moduleClasses[] = $slugParts[0];
            $moduleNames[] = $slugParts[1] ?? "";
        }
        foreach ((new \WHMCS\Module\Module())->getModules($moduleClasses) as $moduleInterface) {
            foreach ($moduleInterface->getApps($moduleNames) as $app) {
                $this->apps[$app->getKey()] = $app;
            }
        }
        $additionalApps = [];
        foreach ((new \WHMCS\Apps\Feed())->additionalApps() as $key => $app) {
            if(array_key_exists($key, $this->apps)) {
            } elseif(0 < count($moduleSlugs) && !in_array($key, $moduleSlugs)) {
            } else {
                $additionalApps[$key] = Model::factoryFromRemoteFeed($app);
            }
        }
        $this->apps = array_merge($this->apps, $additionalApps);
        uasort($this->apps, function ($a, $b) {
            return strcmp($a->getDisplayName(), $b->getDisplayName());
        });
        return $this;
    }
    public function all()
    {
        return $this->apps;
    }
    public function exists($appKey)
    {
        return isset($this->apps[$appKey]);
    }
    public function get($appKey)
    {
        return $this->apps[$appKey] ?? NULL;
    }
    public function active()
    {
        $appHelper = new Utility\AppHelper();
        $appsToReturn = [];
        foreach ($this->apps as $key => $app) {
            if($app->isActive() && !$appHelper->isExcludedFromActiveList($key)) {
                $appsToReturn[$key] = $app;
            }
        }
        return $appsToReturn;
    }
}

?>