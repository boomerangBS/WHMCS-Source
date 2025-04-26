<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\Interfaces;

interface SlugInterface
{
    const INVALID_EMPTY = "slugInvalidEmpty";
    const INVALID_HYPHEN = "slugInvalidHyphen";
    const INVALID_NUMERIC = "slugInvalidFormat";
    public function validateSlugIsUnique($slug);
    public function validateSlugFormat($slug);
    public function autoGenerateUniqueSlug();
    public function getExistingSlugCheck($slug) : \Illuminate\Database\Eloquent\Builder;
}

?>