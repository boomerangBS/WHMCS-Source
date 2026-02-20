<?php

namespace WHMCS\Mail\Incoming;

class MicrosoftMailbox implements MailboxInterface, \Iterator
{
    use MailboxOauthTokenTrait;
    protected $msClient;
    protected $messageIndex = 0;
    protected $messageIds = [];
    public static function createForDepartment(\WHMCS\Support\Department $department = false, $isTest) : MailboxInterface
    {
        $self = new static();
        $self->isTest = $isTest;
        $accessToken = $self->getOauth2AccessToken($department);
        $self->msClient = new \WHMCS\Mail\Providers\Microsoft\MicrosoftGraphMailClient($accessToken);
        return $self;
    }
    public function getMessageCount() : int
    {
        return $this->msClient->getMessageCount();
    }
    public function getAllMessages() : \Iterator
    {
        $this->messageIds = $this->msClient->getMessageIds();
        $this->rewind();
        return $this;
    }
    public function deleteMessage($messageIndex) : void
    {
        if(!isset($this->messageIds[$messageIndex])) {
            throw new \WHMCS\Exception("Undefined message index");
        }
        $this->msClient->deleteMessage($this->messageIds[$messageIndex]);
    }
    public function close() : void
    {
    }
    protected function getMessageId($messageIndex) : int
    {
        if(is_null($messageIndex) || !isset($this->messageIds[$messageIndex])) {
            throw new \WHMCS\Exception("Undefined message index");
        }
        return $this->messageIds[$messageIndex];
    }
    public function getRfcMessage($messageIndex, $messageData)
    {
        return $messageData;
    }
    public function current()
    {
        return $this->msClient->getMessage($this->getMessageId($this->messageIndex));
    }
    public function next()
    {
        $this->messageIndex++;
    }
    public function key()
    {
        return $this->messageIndex;
    }
    public function valid()
    {
        return 0 <= $this->messageIndex && $this->messageIndex < count($this->messageIds);
    }
    public function rewind()
    {
        $this->messageIndex = 0;
    }
}

?>