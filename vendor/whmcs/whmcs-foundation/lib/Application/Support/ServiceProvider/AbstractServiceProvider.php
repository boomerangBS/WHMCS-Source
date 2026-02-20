<?php

namespace WHMCS\Application\Support\ServiceProvider;

abstract class AbstractServiceProvider
{
    protected $app;
    public function __construct(\WHMCS\Container $app)
    {
        $this->app = $app;
    }
    public abstract function register();
}

?>