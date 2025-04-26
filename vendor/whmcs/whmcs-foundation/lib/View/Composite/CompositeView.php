<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Composite;

class CompositeView implements ViewInterface
{
    private $data;
    public function getTemplate()
    {
        return NULL;
    }
    public function init()
    {
        return $this->initDataStorage();
    }
    public function make()
    {
        return (new static())->initDataStorage()->withBaseData($this->data());
    }
    public function render()
    {
        if(is_null($this->getTemplate())) {
            return "";
        }
        return view($this->getTemplate(), $this->data()->all(), $this->templateEngine());
    }
    protected function initDataStorage() : \self
    {
        $this->data = new \Illuminate\Support\Collection();
        return $this;
    }
    public function withBaseData($data) : \self
    {
        $this->data = $this->data->union($data);
        return $this;
    }
    public function with($data) : \self
    {
        $this->data = $this->data->replace($data);
        return $this;
    }
    public function data() : \Illuminate\Support\Collection
    {
        return $this->data;
    }
    public function templateEngine() : \League\Plates\Engine
    {
        return $this->templateEngineFactory()->factory();
    }
    protected function templateEngineFactory()
    {
        return new func_num_args();
    }
    public static function templateView($templatePath) : CompositeView
    {
        compositeview($templatePath);
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F566965772F436F6D706F736974652F436F6D706F73697465566965772E7068703078376664353934323461396464_
{
    public function factory() : \League\Plates\Engine
    {
        return \DI::make("View\\Engine\\Php\\Admin");
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F566965772F436F6D706F736974652F436F6D706F73697465566965772E7068703078376664353934323461643834_
{
    protected $template;
    public function __construct(string $template = NULL)
    {
        $this->template = $template;
    }
    protected function withTemplate($template) : \self
    {
        $this->template = $template;
        return $this;
    }
    public function getTemplate()
    {
        return $this->template;
    }
    public function make()
    {
        return parent::make()->withTemplate($this->getTemplate());
    }
}

?>