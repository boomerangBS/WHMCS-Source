<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Service\Table;

class Service extends \WHMCS\TableModel
{
    public function _execute(array $implementationData = [])
    {
        return $this->getServices($implementationData);
    }
    protected function getServices(array $criteria = [])
    {
        $query = \WHMCS\Database\Capsule::table("tblhosting");
        $query = $this->applyQueryFilters($query, $criteria);
        $inactiveClientsQuery = clone $query;
        if(!$this->hasJoin($inactiveClientsQuery, "tblclients")) {
            $inactiveClientsQuery->join("tblclients", "tblclients.id", "=", "tblhosting.userid");
        }
        $inactiveClientsQuery->whereIn("tblclients.status", ["Inactive", "Closed"]);
        $productCount = $query->count();
        if(\App::isInRequest("show_hidden") && !\App::getFromRequest("show_hidden") || !\App::isInRequest("show_hidden")) {
            $productCount -= clone $inactiveClientsQuery->count();
            $query->where("tblclients.status", "Active");
        }
        $this->getPageObj()->setHiddenCount($inactiveClientsQuery->distinct()->count(["tblclients.id"]));
        $this->getPageObj()->setNumResults($productCount);
        if(!$this->hasJoin($query, "tblclients")) {
            $query->join("tblclients", "tblclients.id", "=", "tblhosting.userid");
        }
        if(!$this->hasJoin($query, "tblproducts")) {
            $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid");
        }
        $orderBy = $this->getPageObj()->getOrderBy();
        if($orderBy == "product") {
            $orderBy = "tblproducts.name";
        } elseif($orderBy == "clientname") {
            $query->orderBy("tblclients.firstname", $this->getPageObj()->getSortDirection());
            $orderBy = "tblclients.lastname";
        }
        $query->orderBy($orderBy, $this->getPageObj()->getSortDirection())->limit($this->getRecordLimit())->offset($this->getRecordOffset());
        $result = $query->get(["tblhosting.*", "tblclients.firstname", "tblclients.lastname", "tblclients.companyname", "tblclients.groupid", "tblclients.currency", "tblproducts.name", "tblproducts.type", "tblproducts.servertype"])->all();
        return json_decode(json_encode($result), true);
    }
    private function hasJoin(\Illuminate\Database\Query\Builder $query, string $table) : \Illuminate\Database\Query\Builder
    {
        return collect($query->joins)->pluck("table")->contains($table);
    }
    private function applyQueryFilters(\Illuminate\Database\Query\Builder $query = [], array $criteria) : \Illuminate\Database\Query\Builder
    {
        if($criteria["clientname"]) {
            if(!$this->hasJoin($query, "tblclients")) {
                $query->join("tblclients", "tblclients.id", "=", "tblhosting.userid");
            }
            $query->where(\WHMCS\Database\Capsule::raw("concat(firstname, ' ', lastname)"), "like", "%" . $criteria["clientname"] . "%");
        }
        if($criteria["type"]) {
            if(!$this->hasJoin($query, "tblproducts")) {
                $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid");
            }
            $query->where("tblproducts.type", $criteria["type"]);
        }
        if($criteria["package"]) {
            if(!$this->hasJoin($query, "tblproducts")) {
                $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid");
            }
            $query->where("tblproducts.id", $criteria["package"]);
        }
        if($criteria["productname"]) {
            if(!$this->hasJoin($query, "tblproducts")) {
                $query->join("tblproducts", "tblproducts.id", "=", "tblhosting.packageid");
            }
            $query->where("tblproducts.name", $criteria["productname"]);
        }
        if($criteria["billingcycle"]) {
            $query->where("billingcycle", $criteria["billingcycle"]);
        }
        if($criteria["server"]) {
            $query->where("server", $criteria["server"]);
        }
        if($criteria["paymentmethod"]) {
            $query->where("paymentmethod", $criteria["paymentmethod"]);
        }
        if($criteria["nextduedate"]) {
            $query->where("nextduedate", toMySQLDate($criteria["nextduedate"]));
        }
        if($criteria["status"]) {
            $query->where("domainstatus", $criteria["status"]);
        }
        if($criteria["domain"]) {
            $query->where("domain", "like", "%" . $criteria["domain"] . "%");
        }
        if($criteria["username"]) {
            $query->where("username", $criteria["username"]);
        }
        if($criteria["dedicatedip"]) {
            $query->where("dedicatedip", $criteria["dedicatedip"]);
        }
        if($criteria["assignedips"]) {
            $query->where("assignedips", "like", "%" . $criteria["assignedips"] . "%");
        }
        if($criteria["id"]) {
            $query->where("tblhosting.id", $criteria["id"]);
        }
        if($criteria["subscriptionid"]) {
            $query->where("subscriptionid", $criteria["subscriptionid"]);
        }
        if($criteria["notes"]) {
            $query->where("tblhosting.notes", "like", "%" . $criteria["notes"] . "%");
        }
        if($criteria["customfield"] && empty($criteria["customfieldvalue"])) {
            $ids = \WHMCS\Database\Capsule::table("tblcustomfields")->join("tblhosting", "tblcustomfields.relid", "=", "tblhosting.packageid")->where("tblcustomfields.type", "product")->where("tblcustomfields.id", $criteria["customfield"])->pluck("tblhosting.id")->all();
            $query->whereIn("tblhosting.id", $ids);
        } elseif($criteria["customfieldvalue"]) {
            if($criteria["customfield"]) {
                $ids = \WHMCS\Database\Capsule::table("tblcustomfieldsvalues")->where("fieldid", (int) $criteria["customfield"])->where("value", "like", "%" . $criteria["customfieldvalue"] . "%")->pluck("relid")->all();
            } else {
                $ids = \WHMCS\Database\Capsule::table("tblcustomfieldsvalues")->join("tblcustomfields", "tblcustomfields.id", "=", "tblcustomfieldsvalues.fieldid")->where("tblcustomfields.type", "product")->where("tblcustomfieldsvalues.value", "like", "%" . $criteria["customfieldvalue"] . "%")->pluck("tblcustomfieldsvalues.relid")->all();
            }
            $query->whereIn("tblhosting.id", $ids);
        }
        return $query;
    }
}

?>