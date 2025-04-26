<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support;

class TicketMask
{
    protected $ticketId = 0;
    protected $mask = "";
    const DEFAULT_TICKET_MASK = "%A%A%A-%n%n%n%n%n%n";
    public function __construct(string $mask = NULL)
    {
        if(!is_null($mask) && self::isValidMask($mask)) {
            $this->mask = $mask;
        } else {
            $this->mask = $this->getDefault();
        }
        return $this;
    }
    public function getDefault()
    {
        $mask = trim(\WHMCS\Config\Setting::getValue("TicketMask"));
        if(!self::isValidMask($mask)) {
            $mask = self::DEFAULT_TICKET_MASK;
        }
        return $mask;
    }
    public static function isValidMask($mask)
    {
        $validMask = true;
        if(strlen($mask) == 0 || $mask == "") {
            $validMask = false;
        }
        return $validMask;
    }
    public function id($ticketId) : TicketMask
    {
        $this->ticketId = $ticketId;
        return $this;
    }
    public function make()
    {
        return $this->generateMask();
    }
    public function unique()
    {
        return $this->generateUniqueMask();
    }
    protected function ticketIdExists($masksToCheck) : array
    {
        return (array) Ticket::whereIn("tid", $masksToCheck)->pluck("tid")->all();
    }
    protected function generateMask()
    {
        $maskString = "";
        $mask = $this->mask;
        $maskLength = strlen($mask);
        for ($i = 0; $i < $maskLength; $i++) {
            $maskValue = $mask[$i];
            if($maskValue == "%") {
                $i++;
                $maskValue .= $mask[$i];
                switch ($maskValue) {
                    case "%A":
                        $maskString .= (new \WHMCS\Utility\Random())->string(0, 1, 0, 0);
                        break;
                    case "%a":
                        $maskString .= (new \WHMCS\Utility\Random())->string(1, 0, 0, 0);
                        break;
                    case "%n":
                        $maskString .= (new \WHMCS\Utility\Random())->string(0, 0, 1, 0);
                        break;
                    case "%y":
                        $maskString .= date("Y");
                        break;
                    case "%m":
                        $maskString .= date("m");
                        break;
                    case "%d":
                        $maskString .= date("d");
                        break;
                    case "%i":
                        $maskString .= $this->ticketId ?: "";
                        break;
                }
            } else {
                $maskString .= $maskValue;
            }
        }
        return $maskString;
    }
    protected function generateUniqueMask()
    {
        $mask = $this->getUniqueMasksFromSet($this->generateMaskSet(5));
        $mask = array_pop($mask);
        if(empty($mask)) {
            for ($i = 0; $i < 100; $i++) {
                $mask = $this->getUniqueMasksFromSet($this->generateMaskSet(5));
                $mask = array_pop($mask);
                if(!empty($mask)) {
                    break;
                }
                if($i === 99) {
                    throw new \WHMCS\Exception\Support\TicketMaskIterationException("Maximum iteration reached generating ticket mask");
                }
            }
        }
        return $mask;
    }
    public function generateMaskSet($numberToGenerate) : array
    {
        $maskArray = [];
        for ($i = 0; $i < $numberToGenerate; $i++) {
            $maskArray[] = $this->generateMask();
        }
        return $maskArray;
    }
    public function getUniqueMasksFromSet($maskArray) : array
    {
        return array_diff($maskArray, $this->ticketIdExists($maskArray));
    }
    public function gatherMaskPossibilities() : int
    {
        $ticketMask = $this->mask;
        $possibilities = 1;
        $maskLength = strlen($ticketMask);
        for ($i = 0; $i < $maskLength; $i++) {
            $maskValue = $ticketMask[$i];
            if($maskValue == "%") {
                $i++;
                $maskValue .= $ticketMask[$i];
                switch ($maskValue) {
                    case "%n":
                        $possibilities = $possibilities * 10;
                        break;
                    case "%a":
                    case "%A":
                        $possibilities = $possibilities * 26;
                        break;
                    case "%m":
                        $possibilities = $possibilities * 12;
                        break;
                    case "%d":
                        $possibilities = $possibilities * 30;
                        break;
                    case "%i":
                        $possibilities = $possibilities + \WHMCS\Environment\DbEngine::MYSQL_INT_MAX_SIGNED;
                        break;
                }
            }
        }
        if(PHP_INT_MAX < $possibilities) {
            return PHP_INT_MAX;
        }
        return $possibilities;
    }
}

?>