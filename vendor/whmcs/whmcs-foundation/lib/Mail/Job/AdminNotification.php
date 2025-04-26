<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Job;

class AdminNotification implements \WHMCS\Scheduling\Contract\JobInterface
{
    use \WHMCS\Scheduling\Jobs\JobTrait;
    const JOB_NAME_GENERIC = "sendAdminNotification.deferred";
    public function send()
    {
        $args = func_get_args();
        if(!function_exists("sendAdminNotificationNow")) {
            include_once ROOTDIR . "/includes/functions.php";
        }
        call_user_func_array("sendAdminNotificationNow", $args);
    }
}

?>