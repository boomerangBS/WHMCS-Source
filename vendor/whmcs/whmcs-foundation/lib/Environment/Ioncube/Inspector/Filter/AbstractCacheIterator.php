<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube\Inspector\Filter;

abstract class AbstractCacheIterator extends \CachingIterator implements \WHMCS\Environment\Ioncube\Contracts\InspectorFilterIteratorInterface
{
    private $phpVersion;
    public function __construct($phpVersion, \WHMCS\Environment\Ioncube\Contracts\InspectorIteratorInterface $iterator, $flags = self::FULL_CACHE)
    {
        $this->setPhpVersion($phpVersion);
        $iterator = $this->getFilterIterator($iterator->getIterator());
        parent::__construct($iterator, $flags);
    }
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }
    public function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }
    public function getFilterIterator(\Iterator $iterator)
    {
        return new \CallbackFilterIterator($iterator, [$this, "accept"]);
    }
    public abstract function accept(\WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $current);
}

?>