<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Composite;

class ViewRenderingView extends CompositeView
{
    protected $views;
    const NO_TEMPLATE_BLOCKNAME = ":no_block:";
    public function init() : \self
    {
        return parent::init()->initViewStorage();
    }
    public function make() : \self
    {
        $instance = parent::make()->initViewStorage();
        $instance->views = $this->views;
        return $instance;
    }
    public function render()
    {
        $renderedViews = $this->renderViews();
        if(is_null($this->getTemplate())) {
            if(1 < $renderedViews->count()) {
                trigger_error("Named blocks not supported for template-less view rendering.", E_USER_WARNING);
            }
            return $renderedViews->get(self::NO_TEMPLATE_BLOCKNAME);
        }
        if($renderedViews->has(self::NO_TEMPLATE_BLOCKNAME)) {
            trigger_error("Surrogate views must map to a block for rendering in template.", E_USER_WARNING);
            unset($renderedViews[self::NO_TEMPLATE_BLOCKNAME]);
        }
        $this->with($renderedViews);
        return parent::render();
    }
    public function addViews($views, string $blockName) : \self
    {
        foreach ($views as $view) {
            $this->addView($view, $blockName);
        }
        return $this;
    }
    public function addView($view, string $blockName) : \self
    {
        if(!$view instanceof ViewInterface) {
            $view = $this->initView($view, $this->data());
        }
        if(is_null($blockName)) {
            $blockName = self::NO_TEMPLATE_BLOCKNAME;
        }
        $newView = $view->make();
        if($this->views->has($blockName)) {
            $views = $this->views->get($blockName);
            $views->push($newView);
        } else {
            $views = collect([$newView]);
        }
        $this->views->put($blockName, $views);
        return $this;
    }
    protected function initView($className, $data) : ViewInterface
    {
        return (new $className())->init()->withBaseData($data)->make();
    }
    protected function renderViews() : \Illuminate\Support\Collection
    {
        return $this->views()->map(function (\Illuminate\Support\Collection $viewCollection) {
            return $viewCollection->map(function (ViewInterface $view) {
                return $view->render();
            })->join(PHP_EOL);
        });
    }
    protected function initViewStorage() : \self
    {
        $this->views = new \Illuminate\Support\Collection();
        return $this;
    }
    public function views() : \Illuminate\Support\Collection
    {
        return $this->views;
    }
}

?>