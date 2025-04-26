<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Utilities\Tools\EmailCampaigns;

class Campaigns extends \WHMCS\TableModel
{
    public function _execute($criteria) : \Illuminate\Support\Collection
    {
        return $this->getCampaigns($criteria);
    }
    protected function getCampaigns($criteria) : \Illuminate\Support\Collection
    {
        $campaigns = \WHMCS\Mail\Campaign::with("admin");
        $this->getPageObj()->setNumResults($campaigns->count());
        $campaigns->orderBy($this->getPageObj()->getOrderBy(), $this->getPageObj()->getSortDirection())->limit($this->getRecordLimit())->offset($this->getRecordOffset());
        return $campaigns->get();
    }
}

?>