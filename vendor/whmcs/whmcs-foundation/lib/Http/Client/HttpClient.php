<?php

namespace WHMCS\Http\Client;

class HttpClient extends \GuzzleHttp\Client
{
    const DEFAULT_TIMEOUT_SEC = 30;
    const DEFAULT_CONNECTION_TEST_TIMEOUT_SEC = 10;
    public function __construct(array $config = [])
    {
        $config = array_merge(static::getLocalDefaults(), $config);
        parent::__construct($config);
    }
    public static function createConnectionTester(array $config = [])
    {
        $config = array_merge(static::getConnectionTestDefaults(), $config);
        return new static($config);
    }
    protected static function getLocalDefaults() : array
    {
        return [\GuzzleHttp\RequestOptions::TIMEOUT => static::DEFAULT_TIMEOUT_SEC];
    }
    protected static function getConnectionTestDefaults() : array
    {
        return [\GuzzleHttp\RequestOptions::TIMEOUT => static::DEFAULT_CONNECTION_TEST_TIMEOUT_SEC];
    }
}

?>