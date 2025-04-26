<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Rewrite;

class RuleSet
{
    public function reduce(array $ruleSet)
    {
        $markers = [File::MARKER_BEGIN, File::MARKER_END];
        foreach ($ruleSet as $key => $value) {
            $value = trim($value);
            if(!$value) {
                unset($ruleSet[$key]);
            } elseif(in_array($value, $markers)) {
                unset($ruleSet[$key]);
            } else {
                $ruleSet[$key] = $value;
            }
        }
        return array_values($ruleSet);
    }
    public function getLegacyRules()
    {
        $rules = ["RewriteEngine On", "# Announcements", "RewriteRule ^announcements/([0-9]+)/[a-z0-9_-]+\\.html\$ ./announcements.php?id=\$1 [L,NC]", "RewriteRule ^announcements\$ ./announcements.php [L,NC]", "# Downloads", "RewriteRule ^downloads/([0-9]+)/([^/]*)\$ ./downloads.php?action=displaycat&catid=\$1 [L,NC]", "RewriteRule ^downloads\$ ./downloads.php [L,NC]", "# Knowledgebase", "RewriteRule ^knowledgebase/([0-9]+)/[a-z0-9_-]+\\.html\$ ./knowledgebase.php?action=displayarticle&id=\$1 [L,NC]", "RewriteRule ^knowledgebase/([0-9]+)/([^/]*)\$ ./knowledgebase.php?action=displaycat&catid=\$1 [L,NC]", "RewriteRule ^knowledgebase\$ ./knowledgebase.php [L,NC]", "# OpenID Discovery Document (http://openid.net/specs/openid-connect-discovery-1_0.html)", "RewriteRule ^.well-known/openid-configuration ./oauth/openid-configuration.php [L,NC]"];
        return $rules;
    }
    public function generateRuleSet()
    {
        $rules = ["<IfModule mod_rewrite.c>", "RewriteEngine on", "", "# RewriteBase is set to \"/\" so rules do not need updating if the", "# installation directory is relocated.  It is imperative that", "# there is also a RewriteCond rule later that can effectively get", "# the actual value by comparison against the request URI.", "# ", "# If there are _any_ other RewriteBase directives in this file,", "# the last entry will take precedence!", "RewriteBase /", "", "# Redirect directories to an address with slash", "RewriteCond %{REQUEST_FILENAME} -d", "RewriteRule ^(.+[^/])\$  \$1/ [R]", "", "# Send all remaining (routable paths) through index.php", "RewriteCond %{REQUEST_FILENAME} !-f", "RewriteCond %{REQUEST_FILENAME} !-d", "# Determine and use the actual base", "RewriteCond \$0#%{REQUEST_URI} ([^#]*)#(.*)\\1\$", "RewriteRule ^.*\$ %2index.php [QSA,L]", "</IfModule>"];
        return $rules;
    }
}

?>