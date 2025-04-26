<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cart\Item;

abstract class AbstractItem implements ItemInterface
{
    public $id;
    public $name = "";
    public $billingCycle;
    public $normalisedBillingCycle;
    public $billingPeriod = 1;
    public $qty = 1;
    public $amount;
    public $recurring;
    public $taxed = false;
    public $initialPeriod = 0;
    public $initialCycle;
    public $uuid;
    public function __construct()
    {
        if(!$this->uuid) {
            $this->uuid = \Illuminate\Support\Str::uuid()->toString();
        }
    }
    public function __clone()
    {
        $this->amount = is_object($this->amount) ? clone $this->amount : $this->amount;
        $this->recurring = is_object($this->recurring) ? clone $this->recurring : $this->recurring;
    }
    public function getUuid()
    {
        return $this->uuid;
    }
    public function setId($id) : \self
    {
        $this->id = $id;
        return $this;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getName()
    {
        return (string) $this->name;
    }
    public function setBillingCycle($billingCycle)
    {
        $billingCycle = (new \WHMCS\Billing\Cycles())->getNormalisedBillingCycle($billingCycle);
        $this->normalisedBillingCycle = $billingCycle;
        if($billingCycle == "quarterly") {
            $this->setBillingPeriod(3);
            $billingCycle = "monthly";
        } elseif($billingCycle == "semiannually") {
            $this->setBillingPeriod(6);
            $billingCycle = "monthly";
        } elseif($billingCycle == "biennially") {
            $this->setBillingPeriod(2);
            $billingCycle = "annually";
        } elseif($billingCycle == "triennially") {
            $this->setBillingPeriod(3);
            $billingCycle = "annually";
        }
        $this->billingCycle = $billingCycle;
        return $this;
    }
    public function getBillingCycle()
    {
        return $this->normalisedBillingCycle;
    }
    public function setBillingPeriod($billingPeriod)
    {
        $this->billingPeriod = $billingPeriod;
        return $this;
    }
    public function getBillingPeriod() : int
    {
        return $this->billingPeriod;
    }
    public function setQuantity($qty) : \self
    {
        $this->qty = $qty;
        return $this;
    }
    public function getQuantity() : int
    {
        return $this->qty;
    }
    public function setAmount(\WHMCS\View\Formatter\Price $amount)
    {
        $this->amount = $amount;
        return $this;
    }
    public function getAmount()
    {
        if($this->amount instanceof \WHMCS\View\Formatter\Price) {
            return $this->amount;
        }
        return new \WHMCS\View\Formatter\Price($this->amount);
    }
    public function setRecurring(\WHMCS\View\Formatter\Price $recurring = NULL)
    {
        return $this->setRecurringAmount($recurring);
    }
    public function getRecurringAmount() : \WHMCS\View\Formatter\Price
    {
        if(!$this->recurring && $this->isRecurring()) {
            return new \WHMCS\View\Formatter\Price(0);
        }
        return $this->recurring;
    }
    public function setRecurringAmount(\WHMCS\View\Formatter\Price $recurring = NULL)
    {
        $this->recurring = $recurring;
        return $this;
    }
    public function setTaxed($taxed)
    {
        $this->taxed = (bool) $taxed;
        return $this;
    }
    public function isTaxed()
    {
        return $this->taxed;
    }
    public function setInitialPeriod($period, $cycle)
    {
        $this->initialPeriod = $period;
        $this->initialCycle = $cycle;
        return $this;
    }
    public function hasInitialPeriod()
    {
        return !is_null($this->initialCycle);
    }
    public function isRecurring()
    {
        return (new \WHMCS\Billing\Cycles())->isRecurring($this->billingCycle);
    }
    public function getType()
    {
        $map = ["Addon" => "Addon", "Product" => "Product", "Domain" => "Domain", "Item" => "Item"];
        $classArray = explode("\\", get_class($this));
        $class = end($classArray);
        if(!isset($map[$class])) {
            throw new \RuntimeException("Unknown cart item type");
        }
        return $map[$class];
    }
}

?>