<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Knowledgebase;

class ArticleCategoryLink extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblknowledgebaselinks";
    public $timestamps = false;
    public function article()
    {
        return $this->belongsTo("\\WHMCS\\Knowledgebase\\Article", "articleid", "id", "article");
    }
    public function category()
    {
        return $this->belongsTo("\\WHMCS\\Knowledgebase\\Category", "categoryid", "id", "category");
    }
}

?>