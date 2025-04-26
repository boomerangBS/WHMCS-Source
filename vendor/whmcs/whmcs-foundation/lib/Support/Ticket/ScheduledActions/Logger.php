<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions;

class Logger
{
    protected $scheduledAction;
    public static function factory(TicketScheduledAction $scheduledAction) : \self
    {
        $static = new static();
        $static->scheduledAction = $scheduledAction;
        return $static;
    }
    public function activity($message = [], array $opts) : void
    {
        $message = sprintf("%s - Ticket ID: %d", $message, $this->scheduledAction->ticket->id);
        logActivity($message, $this->clientId(), $opts);
    }
    public function activityFailure($additionalMessage) : void
    {
        $this->logActivityStatus("failed", $additionalMessage);
    }
    public function activityCancelled($additionalMessage) : void
    {
        $this->logActivityStatus("cancelled", $additionalMessage);
    }
    protected function actionTranslatedName()
    {
        return \AdminLang::trans(sprintf("support.ticket.action.name.%s", strtolower($this->scheduledAction->getActionInstance()::$name)));
    }
    protected function clientId() : int
    {
        return $this->scheduledAction->ticket->client->id ?? 0;
    }
    protected function logActivityStatus($status = NULL, string $additionalMessage) : void
    {
        $message = "";
        if(!empty($additionalMessage)) {
            $message = ": " . $additionalMessage;
        }
        $this->activity(sprintf("Scheduled action '%s' %s%s", $this->actionTranslatedName(), $status, $message));
    }
}

?>