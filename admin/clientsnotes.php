<?php

define("ADMINAREA", true);
require "../init.php";
$action = App::getFromRequest("action");
$sub = App::getFromRequest("sub");
if($action == "edit" || $action == "parseMarkdown") {
    $reqperm = "Add/Edit Client Notes";
} else {
    $reqperm = "View Clients Notes";
}
$adminInterface = new WHMCS\Admin($reqperm);
$adminInterface->setClientsProfilePresets();
$adminInterface->setHelpLink("Clients:Emails/Notes/Logs Tabs");
if($action == "parseMarkdown") {
    $markup = new WHMCS\View\Markup\Markup();
    $content = App::getFromRequest("content");
    $adminInterface->setBodyContent(["body" => "<div class=\"markdown-content\">" . $markup->transform($content, "markdown") . "</div>"]);
    $adminInterface->output();
    WHMCS\Terminus::getInstance()->doExit();
}
$userId = $adminInterface->valUserID(App::getFromRequest("userid"));
$adminInterface->assertClientBoundary($userId);
$adminId = WHMCS\Admin::getID();
$dateNow = WHMCS\Carbon::now()->format("Y-m-d H:i:s");
$note = App::getFromRequest("note");
$sticky = App::getFromRequest("sticky");
$mentionedAdminIds = WHMCS\Mentions\Mentions::getIdsForMentions($note);
if($sub == "add") {
    check_token("WHMCS.admin.default");
    checkPermission("Add/Edit Client Notes");
    WHMCS\Database\Capsule::table("tblnotes")->insert(["userid" => $userId, "adminid" => $adminId, "created" => $dateNow, "modified" => $dateNow, "note" => $note, "sticky" => $sticky]);
    if($mentionedAdminIds) {
        WHMCS\Mentions\Mentions::sendNotification("note", $userId, $note, $mentionedAdminIds);
    }
    logActivity("Added Note", $userId, ["withClientId" => true]);
    redir("userid=" . $userId);
} elseif($sub == "save") {
    check_token("WHMCS.admin.default");
    checkPermission("Add/Edit Client Notes");
    $noteId = (int) App::getFromRequest("id");
    $noteUserId = WHMCS\Database\Capsule::table("tblnotes")->where("id", $noteId)->value("userid");
    if($noteUserId == $userId) {
        WHMCS\Database\Capsule::table("tblnotes")->where("id", $noteId)->update(["note" => $note, "sticky" => $sticky, "modified" => $dateNow]);
        logActivity("Updated Note", $userId, ["withClientId" => true]);
    }
    redir("userid=" . $userId);
} elseif($sub == "delete") {
    check_token("WHMCS.admin.default");
    checkPermission("Delete Client Notes");
    $noteId = (int) App::getFromRequest("id");
    if($noteId) {
        WHMCS\Database\Capsule::table("tblnotes")->where("userid", $userId)->where("id", $noteId)->delete($noteId);
        logActivity("Deleted Note", $userId, ["withClientId" => true]);
    }
    redir("userid=" . $userId);
}
$adminInterface->deleteJSConfirm("doDelete", "clients", "deletenote", "clientsnotes.php?userid=" . $userId . "&sub=delete&id=");
ob_start();
$adminInterface->sortableTableInit("nopagination");
$markup = new WHMCS\View\Markup\Markup();
$adminInterface->addMarkdownEditor("clientNote", "client_note_" . md5($userId . $adminId), "note");
$userNotes = WHMCS\Database\Capsule::table("tblnotes")->where("userid", $userId)->orderBy("modified", "desc")->get();
$tableData = [];
foreach ($userNotes as $userNote) {
    $adminUser = WHMCS\User\Admin::find($userNote->adminid);
    $userNote->adminuser = !is_null($adminUser) ? $adminUser->fullName : "Admin Deleted";
    $markupFormat = $markup->determineMarkupEditor("client_note", "", $userNote->modified);
    $note = $markup->transform($userNote->note, $markupFormat);
    $userNote->note = WHMCS\Mentions\Mentions::doMentionReplacements($note);
    $importantNote = $userNote->sticky ? "highpriority.gif" : "lowpriority.gif";
    $importantNoteLang = AdminLang::trans("clientsummary.importantnote");
    $globalEdit = AdminLang::trans("global.edit");
    $globalDelete = AdminLang::trans("global.delete");
    $importantNoteImage = "<img src=\"images/" . $importantNote . "\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $importantNoteLang . "\">";
    $noteEditHtml = "<a href=\"?userid=" . $userId . "&action=edit&id=" . $userNote->id . "\">\n    <img src=\"images/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $globalEdit . "\">\n</a>";
    $noteDeleteHTML = "<a href=\"#\" onClick=\"doDelete('" . $userNote->id . "');return false\">\n    <img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . $globalDelete . "\">\n</a>";
    $tableData[] = [WHMCS\Carbon::parse($userNote->created)->toAdminDateTimeFormat(), $userNote->note, $userNote->adminuser, WHMCS\Carbon::parse($userNote->modified)->toAdminDateTimeFormat(), $importantNoteImage, $noteEditHtml, $noteDeleteHTML];
}
unset($userNotes);
echo $adminInterface->sortableTable([AdminLang::trans("fields.created"), AdminLang::trans("fields.note"), AdminLang::trans("fields.admin"), AdminLang::trans("fields.lastmodified"), "", "", ""], $tableData);
echo "\n<br>\n\n";
if($action == "edit") {
    $noteId = (int) App::getFromRequest("id");
    $noteAttributes = WHMCS\Database\Capsule::table("tblnotes")->where("userid", $userId)->where("id", $noteId)->first(["note", "sticky"]);
    $important = !empty($noteAttributes->sticky) ? " checked" : "";
    $noteBody = !empty($noteAttributes->note) ? $noteAttributes->note : "";
    echo "    <form method=\"post\" action=\"";
    echo App::getPhpSelf();
    echo "?userid=";
    echo $userId;
    echo "&sub=save&id=";
    echo $noteId;
    echo "\" data-no-clear=\"false\">\n        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n            <tr>\n                <td class=\"fieldarea\">\n                    <textarea id=\"note\" name=\"note\" rows=\"6\" class=\"form-control\">";
    echo $noteBody;
    echo "</textarea>\n                </td>\n                <td align=\"center\" width=\"150\">\n                    <input type=\"submit\" value=\"";
    echo AdminLang::trans("global.savechanges");
    echo "\" class=\"btn btn-primary\"><br />\n                    <div class=\"text-left top-margin-5\">\n                        <label class=\"checkbox-inline\">\n                            <input type=\"checkbox\" class=\"checkbox\" name=\"sticky\" value=\"1\"";
    echo $important;
    echo " />\n                            ";
    echo AdminLang::trans("clientsummary.stickynotescheck");
    echo "                        </label>\n                    </div>\n                </td>\n            </tr>\n        </table>\n    </form>\n";
} else {
    echo "    <form method=\"post\" action=\"";
    echo App::getPhpSelf();
    echo "?userid=";
    echo $userId;
    echo "&sub=add\" data-no-clear=\"false\">\n        <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n            <tr>\n                <td class=\"fieldarea\">\n                    <textarea id=\"note\" name=\"note\" rows=\"6\" class=\"form-control\"></textarea>\n                </td>\n                <td width=\"150\" class=\"text-center\">\n                    <input type=\"submit\" value=\"";
    echo AdminLang::trans("global.addnew");
    echo "\" class=\"btn btn-primary\" /><br />\n                    <div class=\"text-left top-margin-5\">\n                        <label class=\"checkbox-inline\">\n                            <input type=\"checkbox\" class=\"checkbox\" name=\"sticky\" value=\"1\" />\n                            ";
    echo AdminLang::trans("clientsummary.stickynotescheck");
    echo "                        </label>\n                    </div>\n                </td>\n            </tr>\n        </table>\n    </form>\n";
}
$content = ob_get_contents();
ob_end_clean();
$adminInterface->content = $content;
$adminInterface->jquerycode = $jquerycode;
$adminInterface->jscode = $jscode;
$adminInterface->display();

?>