<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Apps;

class AppsController
{
    public function index(\WHMCS\Http\Message\ServerRequest $request, $postLoadAction = NULL, $postLoadParams = [])
    {
        $aInt = new \WHMCS\Admin("Apps and Integrations");
        $aInt->setResponseType(\WHMCS\Admin::RESPONSE_HTML_MESSAGE);
        $aInt->title = \AdminLang::trans("apps.title");
        $aInt->sidebar = "";
        $aInt->icon = "apps";
        $aInt->isSetupPage = true;
        try {
            $aInt->content = view("admin.apps.index", ["assetHelper" => \DI::make("asset"), "heros" => (new \WHMCS\Apps\Hero\Collection((new \WHMCS\Apps\Feed())->heros()))->get(), "postLoadAction" => $postLoadAction, "postLoadParams" => $postLoadParams]);
        } catch (\WHMCS\Exception\Http\ConnectionError $e) {
            $aInt->content = view("admin.apps.index", ["assetHelper" => \DI::make("asset"), "connectionError" => true]);
        } catch (\WHMCS\Exception $e) {
            $aInt->content = view("admin.apps.index", ["assetHelper" => \DI::make("asset"), "renderError" => $e->getMessage()]);
        }
        return $aInt->display();
    }
    public function jumpBrowse(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $this->index($request, "browse", ["category" => $request->get("category")]);
    }
    public function jumpActive(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $this->index($request, "active");
    }
    public function jumpSearch(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $this->index($request, "search");
    }
    public function featured(\WHMCS\Http\Message\ServerRequest $request)
    {
        return new \WHMCS\Http\Message\JsonResponse(["content" => view("admin.apps.featured", ["apps" => new \WHMCS\Apps\App\Collection(), "categories" => new \WHMCS\Apps\Category\Collection()])]);
    }
    public function active(\WHMCS\Http\Message\ServerRequest $request)
    {
        return new \WHMCS\Http\Message\JsonResponse(["content" => view("admin.apps.active", ["apps" => new \WHMCS\Apps\App\Collection()])]);
    }
    public function search(\WHMCS\Http\Message\ServerRequest $request)
    {
        return new \WHMCS\Http\Message\JsonResponse(["data" => collect((new \WHMCS\Apps\App\Collection())->all())->filter(function (\WHMCS\Apps\App\Model $app) {
            return $app->isVisible();
        })->map(function (\WHMCS\Apps\App\Model $app) {
            return ["display_name" => $app->getDisplayName(), "tagline" => $app->getTagline(), "category" => $app->getCategory(), "module_name" => $app->getModuleName(), "keywords" => implode(" ", $app->getKeywords()), "url" => routePath("admin-apps-info", $app->getKey()), "logo_url" => $app->hasLogo() ? routePathWithQuery("admin-apps-logo", $app->getKey(), ["moduleSlug" => $app->getKey()]) : NULL, "badges" => $app->getBadges(), "is_active" => $app->isActive(), "is_updated" => $app->isUpdated(), "is_popular" => $app->isPopular(), "is_featured" => $app->isFeatured(), "is_new" => $app->isNew(), "is_deprecated" => $app->isDeprecated()];
        })->values()->toArray()]);
    }
    public function category(\WHMCS\Http\Message\ServerRequest $request)
    {
        $slug = $request->get("category");
        $apps = new \WHMCS\Apps\App\Collection();
        $categories = new \WHMCS\Apps\Category\Collection();
        $category = $categories->getCategoryBySlug($slug);
        if(is_null($category)) {
            $category = $categories->first();
        }
        return new \WHMCS\Http\Message\JsonResponse(["displayname" => $category->getDisplayName(), "content" => view("admin.apps.category", ["apps" => $apps, "category" => $category, "hero" => $category->getHero($apps), "categories" => $categories])]);
    }
    public function infoModal(\WHMCS\Http\Message\ServerRequest $request)
    {
        $moduleSlug = $request->get("moduleSlug");
        $apps = new \WHMCS\Apps\App\Collection();
        if(!$apps->exists($moduleSlug)) {
            return new \WHMCS\Http\Message\JsonResponse(["body" => view("admin.apps.modal.error", ["errorMsg" => "Module not found. Please try again."])]);
        }
        return new \WHMCS\Http\Message\JsonResponse(["body" => view("admin.apps.modal.info", ["app" => $apps->get($moduleSlug)])]);
    }
    public function logo(\WHMCS\Http\Message\ServerRequest $request)
    {
        $moduleSlug = $request->get("moduleSlug");
        $app = \WHMCS\Apps\App\Collection::getAppBySlug($moduleSlug);
        if(is_null($app)) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "not_found"], 404);
        }
        header("Content-type: image/png");
        header("Pragma: public");
        header("Cache-Control: public,max-age=86400");
        return $app->getLogoContent();
    }
}

?>