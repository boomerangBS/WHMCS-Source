<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway;

class Balance implements BalanceInterface
{
    use CurrencyObjectTrait;
    protected $currencyCode = "";
    protected $amount = 0;
    protected $label = "status.available";
    protected $color = "#5dc560";
    protected function setAmount($amount) : BalanceInterface
    {
        $this->amount = $amount;
        return $this;
    }
    protected function setColor($color) : BalanceInterface
    {
        $this->color = "#" . trim($color, "#");
        return $this;
    }
    protected function setCurrencyCode($currencyCode) : BalanceInterface
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    protected function setLabel($label) : BalanceInterface
    {
        $this->label = $label;
        return $this;
    }
    public static function factory($amount, string $currencyCode = NULL, string $label = NULL, string $color) : BalanceInterface
    {
        $self = new static();
        return $self->setAmount($amount)->setCurrencyCode($currencyCode)->setLabel($label ?: $self->label)->setColor($color ?: $self->color);
    }
    public function colorCodeAsString()
    {
        switch ($this->color) {
            case "#6ecacc":
                return "color-blue";
                break;
            case "#959595":
                return "color-grey";
                break;
            case "#af5dd5":
                return "color-purple";
                break;
            case "#5dc560":
                return "color-green";
                break;
            case "#eaae53":
                return "color-orange";
                break;
            case "#ea5395":
                return "color-pink";
                break;
            case "#63cfd2":
                return "color-cyan";
                break;
        }
    }
    public function getAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->amount, $this->getCurrencyObject());
    }
    public function getColor()
    {
        return $this->color;
    }
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
    public function getLabel()
    {
        return \AdminLang::trans($this->label);
    }
    public function getRawLabel()
    {
        return $this->label;
    }
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
    public function toArray() : array
    {
        return ["amount" => $this->amount, "currencyCode" => $this->currencyCode, "label" => $this->label, "color" => $this->color];
    }
    public static function factoryFromArray($data) : BalanceInterface
    {
        return static::factory($data["amount"], $data["currencyCode"], $data["label"], $data["color"]);
    }
}

?>