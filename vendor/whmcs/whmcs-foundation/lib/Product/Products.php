<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class Products
{
    public function getProducts($groupId = NULL)
    {
        $where = [];
        if($groupId) {
            $where["tblproducts.gid"] = (int) $groupId;
        }
        $products = [];
        $result = select_query("tblproducts", "tblproducts.id,tblproducts.gid,tblproducts.retired,tblproducts.name,tblproductgroups.name AS groupname", $where, "tblproductgroups`.`order` ASC, `tblproducts`.`order` ASC, `name", "ASC", "", "tblproductgroups ON tblproducts.gid=tblproductgroups.id");
        while ($data = mysql_fetch_assoc($result)) {
            $products[] = $data;
        }
        return $products;
    }
}

?>