<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ApplicationLink;

class Provision implements \WHMCS\Scheduling\Contract\JobInterface
{
    use \WHMCS\Scheduling\Jobs\JobTrait;
    public function sync($module)
    {
        $moduleInterface = new \WHMCS\Module\Server();
        $moduleInterface->load($module);
        if($moduleInterface->isApplicationLinkSupported() && $moduleInterface->isApplicationLinkingEnabled()) {
            $moduleInterface->syncApplicationLinksConfigChange();
        }
    }
    public function cleanup($module)
    {
        $moduleInterface = new \WHMCS\Module\Server();
        $moduleInterface->load($module);
        if($moduleInterface->isApplicationLinkSupported() && $moduleInterface->isApplicationLinkingEnabled()) {
            $moduleInterface->cleanupOldApplicationLinks();
        }
    }
    public function cloneScopeLink($applinkId, $oldScopeName, $newScopeName)
    {
        $appLink = ApplicationLink::find($applinkId);
        if(!is_null($appLink) && $appLink->isEnabled) {
            $stdScopes = (new Scope())->getStandardScopes();
            $newScopeDefinition = $stdScopes[$newScopeName];
            if(!is_array($newScopeDefinition)) {
                $newScopeDefinition = ["description" => ""];
            }
            $newLink = Links::firstOrNew(["applink_id" => $appLink->id, "scope" => $newScopeName]);
            $oldLink = $appLink->links()->where("scope", "=", $oldScopeName)->first();
            if($oldLink) {
                $newLink->displayLabel = $oldLink->displayLabel;
                $newLink->isEnabled = 1;
                $newLink->order = $oldLink->order;
            } else {
                $newLink->displayLabel = $newScopeDefinition["description"];
                $newLink->isEnabled = 1;
                $newLink->order = 0;
            }
            $newLink->save();
        }
    }
}

?>