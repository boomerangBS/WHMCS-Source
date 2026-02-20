<?php

namespace WHMCS\Module\Gateway;

class BalanceCollection extends \Illuminate\Support\Collection
{
    public static function factoryFromItems(BalanceInterface ...$balanceArray) : \self
    {
        return new static($balanceArray);
    }
    public function addBalance(BalanceInterface $balance) : \self
    {
        $this->add($balance);
        return $this;
    }
    public static function factoryFromArray($balances) : \self
    {
        $balanceObjects = array_map(function (array $balanceData) {
            return Balance::factoryFromArray($balanceData);
        }, $balances);
        return static::factoryFromItems(...$balanceObjects);
    }
}

?>