<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$clientId = "";
if(isset($client) && $client instanceof WHMCS\User\Client) {
    $clientId = $client->id;
}
echo "<script>\n    let deleteId = jQuery('button.btn-delete-user').data('delete-id');\n    let doRemoveUser = jQuery('#doRemoveUser');\n\n    jQuery(document).ready(function() {\n        jQuery('button.btn-delete-user').on('click', function () {\n            doRemoveUser.attr('data-backdrop', 'false');\n            doRemoveUser.on('show.bs.modal', function () {\n                jQuery(this).parents('.modal-dialog').append('<div class=\"modal-backdrop fade in\"></div>');\n                jQuery(this).css('zIndex', 1060);\n                jQuery('#doRemoveUser-ok').on('click', deleteUser);\n            }).on('hide.bs.modal', function () {\n                jQuery(this).parents('.modal-dialog').children('.modal-backdrop').remove();\n            });\n            doRemoveUser.modal('show');\n        });\n    });\n\n    function deleteUser(e) {\n        e.preventDefault();\n        let btn = jQuery('button.btn-delete-user[data-delete-id=\"' + deleteId + '\"]');\n        if (btn.hasClass('disabled')) {\n            return;\n        }\n        WHMCS.http.jqClient.jsonPost({\n            url: WHMCS.adminUtils.getAdminRouteUrl(\n                '/user/manage/' + deleteId + '/delete'\n            ),\n            data: {\n                token: csrfToken,\n                id: deleteId\n            },\n            success: function () {\n                jQuery('#modalAjax').modal('hide');\n                if (jQuery('.datatable').length !== 0) {\n                    jQuery('a[data-user-id=\"' + deleteId + '\"]').closest('tr').remove();\n                    if (jQuery('#sortabletbl0 tr').length === 2) {\n                        jQuery('#rowNoResults').removeClass('hidden');\n                        jQuery('#rowNoResults').show();\n                    }\n                }\n                jQuery.growl.notice(\n                    {\n                        title: '',\n                        message: '";
echo escape(AdminLang::trans("user.deleted"));
echo "'\n                    }\n                );\n            },\n            warning: function (message) {\n                jQuery.growl.warning(\n                    {\n                        title: '',\n                        message: message\n                    }\n                );\n            },\n            always: function () {\n                doRemoveUser.modal('hide');\n                doRemoveUser.parents('.modal-dialog').children('.modal-backdrop').remove();\n            }\n        });\n    }\n</script>\n\n";
echo WHMCS\View\Helper::confirmationModal("doRemoveUser", AdminLang::trans("user.confirmDelete"));

?>