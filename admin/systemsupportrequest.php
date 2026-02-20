<?php

define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("Configure General Settings");
$aInt->title = $aInt->lang("supportreq", "title");
$aInt->sidebar = "";
$aInt->icon = "support";
ob_start();
$assetHelper = DI::make("asset");
$imagePath = $assetHelper->getImgPath();
echo "\n<p class=\"bigtext\">Our online community is full of helpful resources, from how-to guides on setup, configuration, to advanced troubleshooting, as well as a thriving forum community which prides itself on giving back.</p>\n\n<table style=\"width:100%\">\n<tr>\n<td style=\"width:25%;text-align:center;font-size:24px;color:#00446d;border-right:1px dashed #ccc;padding:20px;vertical-align:middle;\">Read our Docs</td>\n<td style=\"width:25%;text-align:center;font-size:24px;color:#00446d;border-right:1px dashed #ccc;padding:20px;vertical-align:middle;\">Watch Tutorials</td>\n<td style=\"width:25%;text-align:center;font-size:24px;color:#00446d;border-right:1px dashed #ccc;padding:20px;vertical-align:middle;\">Ask the Community</td>\n<td style=\"width:25%;text-align:center;font-size:24px;color:#00446d;padding:20px;vertical-align:middle;\">";
if($licensing->getSupportAccess()) {
    echo "Ask Us";
} else {
    echo " Ask Your Reseller";
}
echo "</td>\n</tr>\n<tr style=\"\">\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;\"><a href=\"https://go.whmcs.com/1893/documentation\" class=\"autoLinked\"><img src=\"";
echo $imagePath;
echo "/get_support/docs.gif\" alt=\"Online Documentation\" width=\"64\" height=\"64\" /></a></td>\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;\"><a href=\"https://go.whmcs.com/1893/documentation\" class=\"autoLinked\"><img src=\"";
echo $imagePath;
echo "/get_support/tutorials.gif\" alt=\"Online Documentation\" width=\"64\" height=\"64\" /></a></td>\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;\"><a href=\"https://whmcs.community/?utm_source=InApp&utm_medium=Get_Help_Screen\" class=\"autoLinked\"><img src=\"";
echo $imagePath;
echo "/get_support/community.gif\" alt=\"Online Documentation\" width=\"64\" height=\"64\" /></a></td>\n<td style=\"width:25%;text-align:center;\"><a href=\"https://www.whmcs.com/submit-a-ticket/\" class=\"autoLinked\"><img src=\"";
echo $imagePath;
echo "/get_support/submitticket.gif\" alt=\"Online Documentation\" width=\"64\" height=\"64\" /></a></td>\n</tr>\n<tr>\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;padding:20px;\"><p>Full of helpful articles and guides on how to use WHMCS</p>\n<div style=\"margin:0 auto;width:100px;\"><a class=\"btn btn-default autoLinked\" href=\"https://go.whmcs.com/1893/documentation\">Go &raquo;</a></div>\n</td>\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;padding:20px;\"><p>Step by step walkthrough&#8217;s on all the most common setup &#038; functionality of WHMCS</p>\n<div style=\"margin:0 auto;width:100px;\"><a class=\"btn btn-default autoLinked\" href=\"https://go.whmcs.com/1893/documentation\">Go &raquo;</a></div>\n</td>\n<td style=\"width:25%;text-align:center;border-right:1px dashed #ccc;padding:20px;\"><p>Home to a very active community of WHMCS users and enthusiasts who are always willing to help resolve issues and discuss new ideas</p>\n<div style=\"margin:0 auto;width:100px;\"><a class=\"btn btn-default autoLinked\" href=\"https://whmcs.community/?utm_source=InApp&utm_medium=Get_Help_Screen\">Go &raquo;</a></div>\n</td>\n<td style=\"width:25%;text-align:center;padding:20px;\"><p>";
if($licensing->getSupportAccess()) {
    echo "Can&#8217;t find what you&#8217;re looking for in our documentation? Let us help! Open a ticket";
} else {
    echo "As your license is provided by " . ($licensing->getKeyData("reseller") ? $licensing->getKeyData("reseller") : "a reseller") . " please contact your license provider for support and assistance";
}
echo "</p>\n<div style=\"margin:0 auto;width:100px;\">";
if($licensing->getSupportAccess()) {
    echo "<a class=\"btn btn-default autoLinked\" href=\"https://www.whmcs.com/submit-a-ticket/\">Go &raquo;</a>";
}
echo "</div>\n</td>\n</tr>\n</table>\n\n<div style=\"margin:20px 0;padding:15px 25px;background-color:#FBF7EA;-moz-border-radius: 10px;-webkit-border-radius: 10px;-o-border-radius: 10px;border-radius: 10px;\">\n    <div style=\"padding:0 0 10px 0;font-size:24px;\">Search our Help Resources</div>\n    <form method=\"post\" action=\"https://go.whmcs.com/1893/documentation\" target=\"_blank\">\n    <input type=\"text\" name=\"search\" style=\"font-size:18px;\" class=\"form-control input-inline input-500\" />\n    <input type=\"submit\" name=\"go\" value=\"Search &raquo;\" style=\"font-size:18px;\" class=\"btn btn-primary\" />\n    </form>\n</div>\n\n<h2>Frequently Asked Questions</h2>\n\n<table width=\"100%\">\n<tr><td width=\"50%\">\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1897/getting-started\" class=\"autoLinked\">How do I get started with WHMCS?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/22/enhancing-security\" class=\"autoLinked\">What additional steps can I take to increase security?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1925/create-product\" class=\"autoLinked\">How do I set up a new product?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/618/domain-pricing\" class=\"autoLinked\">No domains are listed on my order form. Where do I add them?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1929/importassist\" class=\"autoLinked\">How do I add my existing customers to WHMCS?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1933/email-piping\" class=\"autoLinked\">How do I set up email piping?</a></div>\n</td><td width=\"50%\">\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1937/troubleshooting\" class=\"autoLinked\">I'm getting an error. Where do I start looking for an answer?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1941/blank-pages\" class=\"autoLinked\">I'm seeing a blank page. How do I troubleshoot this?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1945/account-creation-failures\" class=\"autoLinked\">Services are not being created automatically. How do I troubleshoot this?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1949/troubleshoot-licensing\" class=\"autoLinked\">I'm getting a license error but am not sure why?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1893/docs\" class=\"autoLinked\">I've forgotten my login details, or my IP has been banned, how do I reset it?</a></div>\n<div style=\"padding:2px 30px;font-size:14px;\"><img src=\"";
echo $imagePath;
echo "/article.gif\" width=\"16\" height=\"16\" /> <a href=\"https://go.whmcs.com/1913/updating\" class=\"autoLinked\">How do I upgrade my WHMCS installation to the latest version?</a></div>\n</td></tr></table>\n\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();

?>