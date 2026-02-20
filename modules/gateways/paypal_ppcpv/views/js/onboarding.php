<?php

$unlinkErrorGeneral = WHMCS\Input\Sanitize::escapeSingleQuotedString(AdminLang::trans("paypalCommerce.unlinkErrorGeneral"));
echo "<script>\nvar ";
echo $module;
echo " = {};\n\n";
foreach ([WHMCS\Module\Gateway\paypal_ppcpv\Environment::LIVE, WHMCS\Module\Gateway\paypal_ppcpv\Environment::SANDBOX] as $envLabel) {
    echo $elements->functionOnboard($envLabel);
    echo " = function (authCode, sharedId) {\n    var nonce = \$('#";
    echo $elements->buttonOnboard($envLabel);
    echo "').data('nonce');\n    ";
    echo $module;
    echo ".onboardingComplete(authCode, sharedId, nonce, '";
    echo $envLabel;
    echo "');\n}\n";
}
echo "\n";
echo $module;
echo ".onboardingComplete = function (authCode, sharedId, nonce, environment) {\n    \$('#";
echo $elements->buttonOnBoardLive();
echo ",#";
echo $elements->buttonOnBoardSandbox();
echo "')\n        .addClass('disabled');\n    WHMCS.http.jqClient.jsonPost({\n        url: \"";
echo routePath("admin-setup-payments-gateways-onboarding-return");
echo "\",\n        data: {\n            token: csrfToken,\n            gateway: '";
echo $module;
echo "',\n            authCode: authCode,\n            sharedId: sharedId,\n            nonce: nonce,\n            env: environment,\n            json: 1\n        },\n        success: function(data) {\n            if (data.success) {\n                window.location = 'configgateways.php?updated=";
echo $module;
echo "&r='\n                    + (new Date()).getTime() + '#";
echo $module;
echo "';\n            } else {\n                window.location = 'configgateways.php?obfailed=1&r='\n                    + (new Date()).getTime();\n            }\n        },\n        error: function() {\n            window.location = 'configgateways.php?obfailed=1&r='\n                + (new Date()).getTime();\n        },\n        fail: function() {\n            window.location = 'configgateways.php?obfailed=1&r='\n                + (new Date()).getTime();\n        }\n    });\n}\n\n";
echo $elements->functionOffBoard();
echo " = function (environment) {\n    WHMCS.http.jqClient.jsonPost({\n        url: '";
echo routePath("admin-paypal_ppcpv-unlink");
echo "',\n        data: {\n            token: csrfToken,\n            env: environment,\n            json: 1\n        },\n        success: function(data) {\n            if (data.success) {\n                window.location = 'configgateways.php?updated=";
echo $module;
echo "&r='\n                    + (new Date()).getTime() + '#";
echo $module;
echo "';\n            } else {\n                jQuery.growl.error(\n                    { title: '', message: '";
echo $unlinkErrorGeneral;
echo "' }\n                );\n            }\n        },\n        error: function() {\n            jQuery.growl.error(\n                { title: '', message: '";
echo $unlinkErrorGeneral;
echo "' }\n            );\n        },\n        fail: function() {\n            jQuery.growl.error(\n                { title: '', message: '";
echo $unlinkErrorGeneral;
echo "' }\n            );\n        }\n    });\n}\n\n";
echo $elements->functionConfirmOffboard();
echo " = function (environment) {\n    var unlinkModal = jQuery('#";
echo $elements->unlinkModal();
echo "'),\n        message = unlinkModal.find('.modal-body');\n\n    unlinkModal.find('#";
echo $elements->unlinkModalConfirm();
echo "')\n        .attr('onclick', '";
echo sprintf("javascript:%s(\\'' + environment + '\\')", $elements->functionOffBoard());
echo "')\n        .removeClass('disabled');\n\n    if (environment === 'sandbox') {\n        message.html('";
echo WHMCS\Input\Sanitize::escapeSingleQuotedString(AdminLang::trans("paypalCommerce.unlinkAccountMsg", [":mode" => "Sandbox"]));
echo "');\n    } else if (environment === 'live') {\n        message.html('";
echo WHMCS\Input\Sanitize::escapeSingleQuotedString(AdminLang::trans("paypalCommerce.unlinkAccountMsg", [":mode" => "Production"]));
echo "');\n    }\n\n    unlinkModal.modal('show');\n}\n</script>\n";

?>