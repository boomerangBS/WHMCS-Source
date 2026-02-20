<?php

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