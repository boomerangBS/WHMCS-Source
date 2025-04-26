<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddMailFwd extends AbstractCommand
{
    protected $command = "AddMailFwd";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $from, string $to)
    {
        $this->setParam("from", $from)->setParam("to", $to);
        parent::__construct($api);
    }
    public static function addList(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, array $list) : array
    {
        $errors = [];
        foreach ($list as $emailAddress => $destination) {
            try {
                (new AddMailFwd($api, $emailAddress, $destination))->execute();
            } catch (\Exception $e) {
                $errors[$emailAddress] = $e->getMessage();
            }
        }
        return $errors;
    }
}

?>