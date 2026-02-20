<?php

echo "<div id=\"admin-dashboard-carousel\"\n     class=\"carousel slide admin-dashboard-carousel\"\n     data-ride=\"carousel\"\n>\n    <div class=\"carousel-inner\" role=\"listbox\">\n        ";
foreach ($promotions as $index => $promotion) {
    echo "            <div class=\"item";
    echo $index === 0 ? " active" : "";
    echo "\">\n                ";
    echo $promotion;
    echo "            </div>\n        ";
}
echo "    </div>\n    ";
if(1 < count($promotions)) {
    echo "        <div class=\"admin-dashboard-carousel-controls\">\n            <a class=\"admin-dashboard-carousel-control\"\n               href=\"#admin-dashboard-carousel\"\n               role=\"button\"\n               data-slide=\"prev\"\n            >\n                <span class=\"glyphicon glyphicon-chevron-left\" aria-hidden=\"true\"></span>\n            </a>\n            <ol class=\"carousel-indicators\">\n                ";
    foreach ($promotions as $index => $promotion) {
        echo "                    <li data-target=\"#admin-dashboard-carousel\"\n                        data-slide-to=\"";
        echo $index;
        echo "\"\n                        ";
        echo $index === 0 ? "class=\"active\"" : "";
        echo "                    ></li>\n                ";
    }
    echo "            </ol>\n            <a class=\"admin-dashboard-carousel-control\"\n               href=\"#admin-dashboard-carousel\"\n               role=\"button\"\n               data-slide=\"next\"\n            >\n                <span class=\"glyphicon glyphicon-chevron-right\" aria-hidden=\"true\"></span>\n            </a>\n        </div>\n    ";
}
echo "</div>";

?>