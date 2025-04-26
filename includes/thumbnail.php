<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "../init.php";
require_once ROOTDIR . "/includes/adminfunctions.php";
require_once ROOTDIR . "/includes/ticketfunctions.php";
$headerLastModifiedNow = function () {
    header(sprintf("Last-Modified: %s GMT", gmdate("D, d M Y H:i:s")));
};
$headerContentType = function ($type) {
    header(sprintf("Content-Type: %s", $type));
};
$noPreviewResponse = function () use($headerLastModifiedNow) {
    static $headerContentType = NULL;
    $headerLastModifiedNow();
    $headerContentType("image/gif");
    echo file_get_contents(DI::make("asset")->getFilesystemImgPath() . "/nothumbnail.gif");
    exit;
};
$fourZeroFourResponse = function () {
    http_response_code(404);
    exit;
};
if(!function_exists("imagecreatefromstring")) {
    logActivity("Unable to generate image thumbnail: GD library is required but appears to be missing from PHP build");
    $noPreviewResponse();
}
$getFromRequest = function ($parameter) {
    if(!App::isInRequest($parameter)) {
        return NULL;
    }
    return (int) App::getFromRequest($parameter);
};
$tid = $getFromRequest("tid");
$rid = $getFromRequest("rid");
$nid = $getFromRequest("nid");
$i = $getFromRequest("i");
if(ecoalesce($tid, $rid, $nid, -1) < 0 || coalesce($i, -1) < 0) {
    $fourZeroFourResponse();
}
$attachments = "";
$ticketId = NULL;
$clientId = NULL;
if(!empty($tid)) {
    $ticket = WHMCS\Support\Ticket::find($tid);
    if(!is_null($ticket)) {
        $ticketId = $tid;
        $clientId = $ticket->clientId;
        $attachments = $ticket->attachment;
    }
    unset($ticket);
} elseif(!empty($rid)) {
    $reply = WHMCS\Support\Ticket\Reply::find($rid);
    if(!is_null($reply)) {
        $ticketId = $reply->ticket->id;
        $attachments = $reply->attachment;
        $clientId = $reply->ticket->clientId;
    }
    unset($reply);
} elseif(!empty($nid)) {
    $note = WHMCS\Support\Ticket\Note::find($nid);
    if(!is_null($note)) {
        $ticketId = $note->ticketid;
        $attachments = $note->attachments;
        $clientId = $note->ticket->clientId;
    }
    unset($note);
}
if(is_null($ticketId)) {
    $fourZeroFourResponse();
}
if(WHMCS\Auth::isLoggedIn() && validateAdminTicketAccess($ticketId) === false) {
} elseif(!is_null(Auth::client()) && Auth::client()->id == $clientId) {
} else {
    $fourZeroFourResponse();
}
$attachments = explode("|", $attachments);
$filename = "";
if(isset($attachments[$i])) {
    $filename = trim($attachments[$i]);
} else {
    $fourZeroFourResponse();
}
$storage = Storage::ticketAttachments();
if(!$storage->has($filename)) {
    $noPreviewResponse();
}
$fileExtension = trim(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ".");
if(!in_array($fileExtension, ["jpeg", "jpg", "png", "gif", "bmp", "wbmp", "gd2"])) {
    $noPreviewResponse();
}
$img = imagecreatefromstring($storage->read($filename));
if(!$img) {
    $noPreviewResponse();
}
$thumbWidth = 200;
$thumbHeight = 125;
$width = imagesx($img);
$height = imagesy($img);
$new_height = $thumbHeight;
$new_width = floor($width * $thumbHeight / $height);
if($new_width < 200) {
    $new_width = 200;
    $new_height = floor($height * $thumbWidth / $width);
} elseif(500 < $new_width) {
    $new_width = 500;
    $new_height = floor($height * $thumbWidth / $width);
}
$tmp_img = imagecreatetruecolor($new_width, $new_height);
imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
$headerLastModifiedNow();
$headerContentType("image/png");
imagepng($tmp_img);
imagedestroy($tmp_img);

?>