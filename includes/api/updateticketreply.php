<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$replyId = (int) App::getFromRequest("replyid");
$message = App::getFromRequest("message");
$created = App::getFromRequest("created");
if(!$replyId) {
    $apiresults = ["result" => "error", "message" => "Reply ID Required"];
} elseif(!$message) {
    $apiresults = ["result" => "error", "message" => "Message is Required"];
} else {
    if($created) {
        try {
            $created = WHMCS\Carbon::parse($created);
            $timeDateNow = WHMCS\Carbon::now();
        } catch (Exception $e) {
            $apiresults = ["result" => "error", "message" => "Invalid Date Format"];
            return NULL;
        }
        if(!$created->lte($timeDateNow)) {
            $apiresults = ["result" => "error", "message" => "Reply creation date cannot be in the future"];
            return NULL;
        }
    }
    if($replyId) {
        try {
            $reply = WHMCS\Support\Ticket\Reply::findOrFail($replyId);
        } catch (Exception $e) {
            $apiresults = ["result" => "error", "message" => "Reply ID Not Found"];
            return NULL;
        }
    }
    $reply->message = $message;
    if(App::isInRequest("markdown")) {
        $useMarkdown = (bool) App::getFromRequest("markdown");
        $editor = "plain";
        if($useMarkdown) {
            $editor = "markdown";
        }
        $reply->editor = $editor;
    }
    if($created && $created instanceof WHMCS\Carbon) {
        $reply->date = $created;
    }
    $reply->save();
    $apiresults = ["result" => "success", "replyid" => $replyId];
}

?>