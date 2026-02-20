<?php

namespace WHMCS\Module\Addon\ProjectManagement;

class Container
{
    private $di;
    private static $instance;
    private function __construct()
    {
    }
    public static function getInstance() : \self
    {
        if(is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->di = new \Illuminate\Container\Container();
        }
        return self::$instance;
    }
    public function make($abstract, array $parameters = [])
    {
        return $this->di->make($abstract, $parameters);
    }
}

?>