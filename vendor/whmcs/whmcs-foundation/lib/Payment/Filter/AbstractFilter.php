<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Filter;

abstract class AbstractFilter implements FilterInterface
{
    public function getFilteredIterator(\Iterator $iterator)
    {
        return new Iterator\CallbackIterator($iterator, [$this, "filter"]);
    }
    public abstract function filter(\WHMCS\Payment\Adapter\AdapterInterface $adapter);
}

?>