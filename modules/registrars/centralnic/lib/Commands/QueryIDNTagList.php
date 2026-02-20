<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class QueryIDNTagList extends AbstractCommand
{
    protected $command = "QueryIDNTagList";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $tld)
    {
        $this->setParam("zone", $tld);
        parent::__construct($api);
    }
}

?>