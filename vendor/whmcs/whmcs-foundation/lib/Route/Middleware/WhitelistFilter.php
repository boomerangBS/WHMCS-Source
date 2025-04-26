<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

class WhitelistFilter implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use Strategy\DelegatingMiddlewareTrait;
    protected $filterList;
    protected $strictFilter = true;
    public function __construct($strictFilter = true, array $filterList = [])
    {
        $this->setStrictFilter($strictFilter)->setFilterList($filterList);
    }
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $isAllowed = false;
        if($this->isAllowed($request)) {
            $request = $this->whitelistRequest($request);
            $isAllowed = true;
        } else {
            $request = $this->blacklistRequest($request);
        }
        if(!$isAllowed && $this->isStrictFilter()) {
            return new \WHMCS\Exception\HttpCodeException("Bad Request For Endpoint");
        }
        return $delegate->process($request);
    }
    protected function isAllowed(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        foreach ($this->getFilterList() as $basePath) {
            if(strpos($path, $basePath) === 0) {
                return true;
            }
        }
        return false;
    }
    protected function getFilterList()
    {
        return $this->filterList;
    }
    protected function setFilterList(array $filterList)
    {
        $this->filterList = $filterList;
        return $this;
    }
    protected function isStrictFilter()
    {
        return $this->strictFilter;
    }
    protected function setStrictFilter($strictFilter)
    {
        $this->strictFilter = $strictFilter;
        return $this;
    }
    protected function whitelistRequest(\Psr\Http\Message\ServerRequestInterface $request)
    {
        return $request;
    }
    protected function blacklistRequest(\Psr\Http\Message\ServerRequestInterface $request)
    {
        return $request;
    }
}

?>