<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

class ActionsList
{
    protected $actions = ["WHMCS\\Support\\Ticket\\Actions\\PinToTop", "WHMCS\\Support\\Ticket\\Actions\\UpdateDepartment", "WHMCS\\Support\\Ticket\\Actions\\UpdatePriority", "WHMCS\\Support\\Ticket\\Actions\\UpdateStatus", "WHMCS\\Support\\Ticket\\Actions\\AssignTicket", "12" => "WHMCS\\Support\\Ticket\\Actions\\TestResult"];
    protected $actionNameMap;
    const PRIORITY_UNDEFINED = -1;
    public function getActions() : array
    {
        if(!is_null($this->actionNameMap)) {
            return $this->actionNameMap;
        }
        foreach ($this->actions as $class) {
            $this->actionNameMap[$class::name()] = $class;
        }
        return $this->actionNameMap;
    }
    public static function getActionClass(string ...$actionIdentifier)
    {
        $classes = [];
        $actionMap = (new static())->getActions();
        foreach ($actionIdentifier as $i => $identifier) {
            if(!isset($actionMap[$identifier]) || !class_exists($actionMap[$identifier], true)) {
                throw new \Exception("Unknown action '" . $identifier . "'");
            }
            $classes[$i] = $actionMap[$identifier];
        }
        return count($classes) == 1 ? array_pop($classes) : $classes;
    }
    public function getSchema() : array
    {
        $actionOptions = [];
        foreach ($this->getActions() as $classname) {
            if($classname::name() == "TestResult") {
            } else {
                $actionOptions[] = (new $classname())->schema();
            }
        }
        return $actionOptions;
    }
    public function getActionPriority(string ...$actionClasses)
    {
        $priorities = [];
        $priorityMap = array_flip($this->actions);
        foreach ($actionClasses as $i => $class) {
            if(isset($priorityMap[$class])) {
                $priorities[$i] = $priorityMap[$class];
            } else {
                $priorities[$i] = -1;
            }
        }
        return count($priorities) == 1 ? array_pop($priorities) : $priorities;
    }
    public function compareTicketActionPriority(\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction $a, $b) : int
    {
        $actionMap = $this->getActions();
        return $this->comparePriority(...$this->getActionPriority($actionMap[$a->action] ?? "", $actionMap[$b->action] ?? ""));
    }
    public function compareActionPriority($a, string $b) : int
    {
        return $this->comparePriority(...$this->getActionPriority($a, $b));
    }
    protected function comparePriority($a, int $b) : int
    {
        if($a == $b) {
            return 0;
        }
        if($a == self::PRIORITY_UNDEFINED) {
            return 1;
        }
        if($b == self::PRIORITY_UNDEFINED) {
            return -1;
        }
        return $a < $b ? -1 : 1;
    }
}

?>