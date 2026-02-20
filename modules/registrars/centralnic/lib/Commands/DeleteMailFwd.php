<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class DeleteMailFwd extends AbstractCommand
{
    protected $command = "DeleteMailFwd";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $from, string $to)
    {
        $this->setParam("from", $from)->setParam("to", $to);
        parent::__construct($api);
    }
    public static function deleteList(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, array $list) : array
    {
        $errors = [];
        foreach ($list as $emailAddress => $destination) {
            try {
                (new DeleteMailFwd($api, $emailAddress, $destination))->execute();
            } catch (\Exception $e) {
                $errors[$emailAddress] = $e->getMessage();
            }
        }
        return $errors;
    }
}

?>