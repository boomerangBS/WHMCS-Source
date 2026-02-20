<?php

namespace WHMCS\Updater\Version;

class Version8100release1 extends IncrementalVersion
{
    protected $updateActions = ["updateInvoiceAutoCancellationCronTask", "removeRefreshAppsFeedCronTask", "onUpgradeSelectNetPromotorScoreTestVariant"];
    public function updateInvoiceAutoCancellationCronTask()
    {
        $query = \WHMCS\Database\Capsule::table("tbltask")->where([["class_name", "WHMCS\\Cron\\Task\\InvoiceAutoCancellation"], ["name", "!=", "Overdue Invoice Cancellations"], ["description", "!=", "Cancel Overdue Invoices"]]);
        if(0 < $query->count()) {
            $query->update(["name" => "Overdue Invoice Cancellations", "description" => "Cancel Overdue Invoices"]);
        }
        return $this;
    }
    protected function removeRefreshAppsFeedCronTask() : \self
    {
        $appFeedTask = \WHMCS\Database\Capsule::table("tbltask")->where("class_name", "WHMCS\\Cron\\Task\\RefreshAppsFeed");
        $appFeedTaskID = $appFeedTask->value("id");
        if(!is_null($appFeedTaskID)) {
            \WHMCS\Database\Capsule::table("tbltask_status")->where("task_id", $appFeedTaskID)->delete();
            $appFeedTask->delete();
        }
        return $this;
    }
    protected function onUpgradeSelectNetPromotorScoreTestVariant() : \self
    {
        self::selectNetPromotorScoreTestVariant();
        return $this;
    }
    public static function selectNetPromotorScoreTestVariant() : void
    {
        \WHMCS\Admin\Survey\Retently\v1\GlobalNps::ensureSettingVariant();
        \WHMCS\Admin\Survey\Retently\v1\MarketConnectCsat::ensureSettingVariant();
    }
}

?>