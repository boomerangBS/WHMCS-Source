<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\User;

class UsersFilter extends \WHMCS\TableModel
{
    public function _execute(array $implementationData = [])
    {
        return $this->getUsers($implementationData);
    }
    protected function getUsers(array $criteria = NULL)
    {
        $users = \WHMCS\User\User::where("id", "!=", 0);
        if(!empty($criteria["criteria"])) {
            $users->where(\WHMCS\Database\Capsule::raw("CONCAT_WS(' ', first_name, last_name)"), "like", "%" . $criteria["criteria"] . "%")->orWhere("email", "like", "%" . $criteria["criteria"] . "%");
        }
        $this->getPageObj()->setNumResults($users->count());
        $users->orderBy($this->getPageObj()->getOrderBy(), $this->getPageObj()->getSortDirection())->limit($this->getRecordLimit())->offset($this->getRecordOffset());
        return $users->get();
    }
}

?>