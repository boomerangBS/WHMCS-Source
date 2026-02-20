<?php

namespace WHMCS\Support\Ticket\Actions;

class TestResult extends AbstractAction
{
    use JSONParametersTrait;
    protected $testResult = "";
    public static $name = "TestResult";
    public function execute()
    {
        if($this->testResult() == \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_FAILED) {
            return false;
        }
        return true;
    }
    public function testResult()
    {
        return $this->testResult;
    }
    public function init(\WHMCS\Support\Ticket $ticket, array $parameters)
    {
        $this->ticket = $ticket;
        $parameters = $this->defaultToComplete($parameters);
        $this->assertParameters($parameters);
        $this->testResult = $parameters["testResult"];
        return $this;
    }
    protected function getParametersToSerialize() : array
    {
        return ["testResult" => $this->testResult];
    }
    private function defaultToComplete(array $parameters)
    {
        if(!isset($parameters["testResult"])) {
            $parameters["testResult"] = \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_COMPLETED;
        }
        return $parameters;
    }
    public function assertParameters(array $parameters)
    {
        if(!in_array($parameters["testResult"], [\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_COMPLETED, \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::STATUS_FAILED])) {
            throw new \InvalidArgumentException("Parameter 'testResult' is not valid");
        }
        return $this;
    }
    public function detailString()
    {
        return "Resolve Execution As";
    }
}

?>