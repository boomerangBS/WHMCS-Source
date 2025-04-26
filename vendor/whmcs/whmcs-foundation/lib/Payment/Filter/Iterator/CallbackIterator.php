<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Filter\Iterator;

class CallbackIterator extends \FilterIterator
{
    private $callback;
    public function __construct(\Iterator $iterator, $conditionalCallback)
    {
        $this->setCallback($conditionalCallback);
        parent::__construct($iterator);
    }
    public function accept()
    {
        $item = $this->getInnerIterator()->current();
        if(call_user_func($this->getCallback(), $item)) {
            return true;
        }
        return false;
    }
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
    public function getCallback()
    {
        return $this->callback;
    }
}

?>