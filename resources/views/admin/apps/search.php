<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div class=\"app-category-title\">\n    <h2>";
echo AdminLang::trans("apps.searchResultsTitle");
echo "</h2>\n    <p id=\"searchMatchesFound\" class=\"lead\" style=\"display: none\">\n        <span id=\"searchResultsCount\">0</span> ";
echo AdminLang::trans("apps.searchMatchesFound");
echo "    </p>\n    <p id=\"waitingSearchResultsPlaceholder\" class=\"lead\">&nbsp;</p>\n</div>\n\n<div class=\"app-wrapper\">\n    <div id=\"activeContentPane\">\n        <div class=\"loader\">\n            ";
echo AdminLang::trans("global.loading");
echo "        </div>\n    </div>\n</div>\n\n<div class=\"app-wrapper min-search-term hidden\">\n    <span>\n        ";
echo AdminLang::trans("apps.searchMinSearchTerm");
echo "    </span>\n</div>\n<div class=\"app-wrapper no-results-found hidden\">\n    <span>\n        ";
echo AdminLang::trans("apps.searchNoResultsFound");
echo "    </span>\n</div>\n\n<div class=\"search-wrapper hidden\">\n    <div class=\"app-wrapper clearfix\">\n        <h3>";
echo AdminLang::trans("apps.recommendedTitle");
echo "</h3>\n        <div class=\"apps search-apps-featured\">\n        </div>\n    </div>\n\n    <div class=\"app-wrapper clearfix\">\n        <div class=\"apps search-apps-regular search-apps-load-target\">\n        </div>\n    </div>\n</div>\n";

?>