<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Smarty\Resource;

class MailMessage extends \Smarty_Resource_Custom
{
    protected $message;
    public function __construct(\WHMCS\Mail\Message $message)
    {
        $this->setMessage($message);
    }
    protected function setMessage(\WHMCS\Mail\Message $message)
    {
        $this->message = $message;
        return $this;
    }
    protected function getMessage()
    {
        return $this->message;
    }
    protected function fetch($name, &$source, &$mtime)
    {
        $mtime = time();
        switch ($name) {
            case "subject":
                $source = $this->getMessage()->getSubject();
                break;
            case "message":
                $source = $this->getMessage()->getBodyWithoutCSS();
                break;
            case "plaintext":
                $source = $this->getMessage()->getPlainText();
                break;
            default:
                $source = NULL;
                $mtime = NULL;
        }
    }
}

?>