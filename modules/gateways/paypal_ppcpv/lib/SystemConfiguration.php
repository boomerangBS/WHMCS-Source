<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
class SystemConfiguration
{
    protected $application;
    protected static $singleton;
    public static function singleton(\WHMCS\Application $app)
    {
        if(!is_null(self::$singleton)) {
            return self::$singleton;
        }
        self::$singleton = new self($app);
        return self::$singleton;
    }
    public function __construct(\WHMCS\Application $app)
    {
        $this->application = $app;
    }
    public function app() : \WHMCS\Application
    {
        return $this->application;
    }
}

?>