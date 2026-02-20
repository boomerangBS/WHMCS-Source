<?php

$element = new func_num_args($module);
echo "<div id=\"";
echo $element->container;
echo "\" style=\"display:none\">\n    <div id=\"";
echo $element->buttonContainer;
echo "\"></div>\n    <div id=\"";
echo $element->payerContainer;
echo "\" style=\"display:none\">\n        <div class=\"payer-label\">\n            <div class=\"logo\">\n                <img src=\"";
echo DI::make("asset")->getWebRoot();
echo "/modules/gateways/paypal_ppcpv/logo-icon.png\">\n            </div>\n            <span class=\"payer-email\">\n                ";
echo Lang::trans("paypalCommerce.payerApprovedPlaceholder");
echo "            </span>\n        </div>\n    </div>\n    <div class=\"payment-instructions\" style=\"display:none\">\n        ";
echo Lang::trans("paypalCommerce.paymentInstructions");
echo "    </div>\n    <div class=\"approval-instructions\" style=\"display:none\">\n        ";
echo Lang::trans("paypalCommerce.payerApprovedInstructions");
echo "    </div>\n</div>\n<script type=\"application/javascript\">\nlet ";
echo $module;
echo " = {};\n\n";
if($renderSource == "invoice-pay") {
    echo $module;
    echo ".invoiceCCElements = (() => {\n    let ccForm = jQuery('div.cc-payment-form');\n    return jQuery('#inputCardCvv').closest('div.form-group', ccForm)\n        .add(jQuery('#inputCardNumber').closest('div.form-group', ccForm))\n        .add(jQuery('#billingAddressChoice'))\n        .add(jQuery('#inputDescriptionContainer'))\n        .add(jQuery('#inputCardExpiry').closest('div.form-group', ccForm))\n        .add(jQuery('#inputCardStart').closest('div.form-group', ccForm))\n        .add(jQuery('#inputIssueNum').closest('div.form-group', ccForm));\n})();\n\n";
    echo $module;
    echo ".registerUI = function () {\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewayInit(function (metadata, element) {\n            jQuery('#frmPayment').off('submit', validateCreditCardInput);\n            jQuery('#newCCInfo').on('ifChecked.";
    echo $module;
    echo "', function (event) {\n                ";
    echo $module;
    echo ".invoiceCCElements.hide();\n            });\n            ";
    echo $module;
    echo ".invoiceCCElements.hide();\n            ";
    echo $module;
    echo ".renderButtons();\n            WHMCS.payment.display.show(jQuery(";
    echo $element->container();
    echo "));\n        })\n        .onCheckoutFormSubmit(function (metadata, element) {\n            if (!jQuery('#newCCInfo').is(':checked')) {\n                return;\n            }\n            metadata.event.preventDefault();\n            let dataOrderId = jQuery(";
    echo $element->container();
    echo ").data('orderid');\n            if (typeof dataOrderId === 'undefined' || dataOrderId.length == 0) {\n                WHMCS.payment.display.submitReset(metadata._source);\n                WHMCS.payment.display.errorShow('";
    echo addslashes(Lang::trans("paypalCommerce.error.noAccount"));
    echo "');\n                scrollToGatewayInputError();\n                throw new Error('No PayPal Order ID present');\n            }\n            fetch('";
    echo $routeOnApprove;
    echo "', {\n                method: 'post',\n                headers: {\n                    'content-type': 'application/json'\n                },\n                body: JSON.stringify({\n                    token: csrfToken,\n                    orderid: dataOrderId,\n                    invoiceid: jQuery('input[name=\"invoiceid\"]').val()\n                })\n            })\n            .then(function (res) {\n                return res.json();\n            })\n            .then(function (data) {\n                if (data.success) {\n                    if (data.redirectUrl) {\n                        window.location = data.redirectUrl;\n                    } else {\n                        window.location.reload();\n                    }\n                } else {\n                    if (data.status === 'declined') {\n                        throw new Error('";
    echo addslashes(Lang::trans("genericPaymentDeclined"));
    echo "');\n                    }\n                    throw new Error(data.error);\n                }\n            })\n            .catch(function (err) {\n                WHMCS.payment.display.submitReset(metadata._source);\n                WHMCS.payment.display.errorShow(err);\n                scrollToGatewayInputError();\n            });\n        });\n}\n\n";
} elseif($renderSource == "checkout") {
    echo "\n";
    echo $module;
    echo ".registerUI = function () {\n    let toggleSelectors = '#newCardInfo,#newCardSaveSettings,#existingCardInfo';\n    let paymentForm = jQuery('#frmCheckout');\n    let newPayMethodOption = jQuery('.cc-input-container label.radio-inline');\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewayInit(function (metadata, element) {\n            jQuery(";
    echo $element->container();
    echo ").insertAfter(jQuery('#newCardInfo'));\n            ";
    echo $module;
    echo ".renderButtons();\n        })\n        .onGatewaySelected(function (metadata, element) {\n            let initialNewPayMethodOption = jQuery('input[name=ccinfo][checked]');\n            jQuery('#new')\n                .on('ifChecked.";
    echo $module;
    echo "', function () {\n                    jQuery(toggleSelectors).hide();\n                    jQuery(";
    echo $element->container();
    echo ").slideDown();\n                })\n                .on('ifUnchecked.";
    echo $module;
    echo "', function () {\n                    jQuery(";
    echo $element->container();
    echo ").slideUp();\n                });\n            jQuery('input[name=\"ccinfo\"]')\n                .on('ifChecked.";
    echo $module;
    echo "', function () {\n                    ";
    echo $module;
    echo ".updateCheckedOption(jQuery(this));\n                })\n                .on('ifUnchecked.";
    echo $module;
    echo "', function () {\n                    ";
    echo $module;
    echo ".updateUncheckedOption(jQuery(this));\n                });\n            if (initialNewPayMethodOption.length > 0) {\n                ";
    echo $module;
    echo ".updateCheckedOption(initialNewPayMethodOption);\n            } else {\n                ";
    echo $module;
    echo ".updateCheckedOption(jQuery('input[name=ccinfo]').closest('div.checked input'));\n            }\n            jQuery(toggleSelectors).hide();\n            paymentForm.addClass('";
    echo $element->paymentFormClass;
    echo "');\n            if (jQuery('#new').is(':checked')) {\n                jQuery(";
    echo $element->container();
    echo ").show();\n            }\n            ";
    echo $module;
    echo ".updateNewPayMethodOption(newPayMethodOption);\n            ";
    echo $module;
    echo ".displayInstructions(jQuery('#creditCardInputFields'));\n        })\n        .onGatewayUnselected(function (metadata, element) {\n            jQuery('#new').off('ifChecked.";
    echo $module;
    echo "')\n                .off('ifUnchecked.";
    echo $module;
    echo "');\n            jQuery('input[name=\"ccinfo\"]').off('ifUnchecked.";
    echo $module;
    echo "');\n            jQuery('.cc-input-container .selected').each(function () {\n                jQuery(this).removeClass('selected');\n            });\n            ";
    echo $module;
    echo ".restoreNewPayMethodOption(newPayMethodOption);\n            jQuery(toggleSelectors).show();\n            jQuery(";
    echo $element->container();
    echo ").hide();\n            jQuery('.payment-instructions').hide();\n            paymentForm.removeClass('";
    echo $element->paymentFormClass;
    echo "');\n        })\n        .onCheckoutFormSubmit(function (metadata, element) {\n            if (!jQuery('#new').is(':checked')) {\n                return;\n            }\n            let dataOrderId = jQuery(";
    echo $element->container();
    echo ").data('orderid');\n            if (typeof dataOrderId === 'undefined' || dataOrderId.length == 0) {\n                metadata.event.preventDefault();\n                WHMCS.payment.display.submitReset(metadata._source);\n                WHMCS.payment.display.errorShow('";
    echo addslashes(Lang::trans("paypalCommerce.error.noAccount"));
    echo "');\n                scrollToGatewayInputError();\n                throw new Error('No PayPal Order ID present');\n            }\n        });\n}\n\n";
} elseif($renderSource == "payment-method-add") {
    echo "\n";
    echo $module;
    echo ".paymentAdditionCCElements = (() => {\n    return jQuery('div.fieldgroup-creditcard')\n        .add(jQuery('div.fieldgroup-auxfields'));\n})();\n";
    echo $module;
    echo ".registerUI = function () {\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewaySelected(function (metadata, element) {\n            ";
    echo $module;
    echo ".paymentAdditionCCElements.hide();\n            ";
    echo $module;
    echo ".renderButtons();\n            WHMCS.payment.display.show(jQuery(";
    echo $element->container();
    echo "));\n        })\n        .onGatewayUnselected(function (metadata, element) {\n            WHMCS.payment.display.hide(jQuery(";
    echo $element->container();
    echo "));\n        });\n}\n";
} else {
    echo $module;
    echo ".registerUI = function () {};\n";
}
echo "\n";
echo $module;
echo ".selectNewAccount = function () {\n    ";
if($renderSource == "checkout") {
    echo "    jQuery('#newCardSaveSettings').hide();\n    ";
} elseif($renderSource == "invoice-pay") {
    echo "    jQuery('#newCCInfo').iCheck('check');\n    ";
}
echo "};\n\n";
echo $module;
echo ".onCreateOrder = function (data) {\n    ";
echo $module;
echo ".selectNewAccount();\n    return fetch('";
echo $routeCreateOrder;
echo "', {\n        method: 'post',\n        headers: {\n            'content-type': 'application/json'\n        },\n        body: JSON.stringify({\n            token: csrfToken,\n            source: data.paymentSource,\n            ";
if($renderSource == "invoice-pay") {
    echo "            invoiceid: jQuery('input[name=\"invoiceid\"]').val()\n            ";
}
echo "        })\n    }).then(function(res) {\n        return res.json();\n    }).then(function(data) {\n        return data.id;\n    });\n};\n\n";
echo $module;
echo ".onCreateSetupToken = function (createSetupTokenFields) {\n    return function () {\n        ";
echo $module;
echo ".selectNewAccount();\n        return new Promise(function (resolve, reject) {\n            WHMCS.http.jqClient.jsonPost({\n                url: '";
echo $routeCreateSetupToken;
echo "',\n                data: {\n                    token: csrfToken,\n                    ...createSetupTokenFields(),\n                },\n                success: function (response) {\n                    resolve(response);\n                },\n                error: function (response) {\n                    reject(response);\n                }\n            });\n        }).then(function (response) {\n            return response.id;\n        });\n    }\n};\n\n";
echo $module;
echo ".onCreateSetupTokenApproveRetrieveToken = function (setupToken) {\n    ";
echo $module;
echo ".selectNewAccount();\n    return new Promise(function (resolve, reject) {\n        WHMCS.http.jqClient.jsonPost({\n            url: '";
echo $routeGetSetupToken;
echo "',\n            data: {\n                token: csrfToken,\n                setuptoken: setupToken.vaultSetupToken,\n            },\n            success: function (response) {\n                resolve(response);\n            },\n            error: function (response) {\n                reject(response);\n            }\n        });\n    }).then(function (response) {\n        if (response.success) {\n                        ";
echo $module;
echo ".populatePayer(response);\n            ";
echo $module;
echo ".showApprovedPayer();\n            jQuery(";
echo $element->container();
echo ").data('orderid', 'NOTAPPLICABLE');\n        }\n    });\n\n};\n\n";
echo $module;
echo ".populatePayer = function (payer) {\n    let payerContainer = jQuery(";
echo $element->payerContainer();
echo ");\n    let payerInfo = payer?.email_address;\n    if (payerInfo === undefined || payerInfo === '') {\n        return;\n    }\n    payerContainer.find('.payer-email')\n        .html(payerInfo);\n}\n\n";
echo $module;
echo ".showApprovedPayer = function () {\n    let newPayMethodOption = jQuery(\n        '.cc-input-container label.radio-inline, .cc-payment-form .paymethod-info:not([data-paymethod-id]) label'\n    );\n    jQuery(";
echo $element->container();
echo ").attr('data-";
echo $element->approvedAttribute;
echo "', 1);\n    newPayMethodOption.contents()\n        .last()[0].textContent = '';\n    newPayMethodOption.append(jQuery('.payer-label').clone());\n    jQuery('.approval-instructions').show();\n    ";
if($renderSource == "checkout") {
    echo "    newPayMethodOption.closest('ul')\n        .slideDown()\n        .find('#new')\n        .iCheck('check');\n    ";
} elseif($renderSource == "invoice-pay") {
    echo "    newPayMethodOption.closest('div')\n        .slideDown()\n        .find('#new')\n        .iCheck('check');\n    ";
}
echo "    jQuery(";
echo $element->buttonContainer();
echo ").slideUp();\n    WHMCS.payment.display.errorClear();\n}\n\n";
echo $module;
echo ".onApprove = function (data, actions) {\n        actions.order.get()\n        .then(function (order) {\n          return order?.payer;\n        })\n        .then(";
echo $module;
echo ".populatePayer)\n        .then(";
echo $module;
echo ".showApprovedPayer);\n\n        ";
if($renderSource == "invoice-pay") {
    echo "    jQuery(";
    echo $element->container();
    echo ").data('orderid', data.orderID);\n    ";
} elseif($renderSource == "checkout") {
    echo "    jQuery(";
    echo $element->container();
    echo ").data('orderid', data.orderID);\n    ";
}
echo "};\n\n";
echo $module;
echo ".renderButtons = function () {\n        (async() => {\n        while (typeof paypal === 'undefined') {\n            await new Promise(resolve => setTimeout(resolve, 250));\n        }\n        (() => {\n            paypal.Buttons({\n                commit: false,\n                style: {\n                    color: 'white',\n                    layout: 'vertical',\n                    size: 'responsive',\n                    label: 'pay',\n                },\n                ";
if($requiresPayment) {
    echo "                createOrder: ";
    echo $module;
    echo ".onCreateOrder,\n                onApprove: ";
    echo $module;
    echo ".onApprove,\n                ";
} else {
    echo "                createVaultSetupToken: ";
    echo $module;
    echo ".onCreateSetupToken(\n                    function () {\n                        return {\n                            ...{\n                                custtype: jQuery('#inputCustType').val(),\n                            },\n                        };\n                    },\n                ),\n                onApprove: ";
    echo $module;
    echo ".onCreateSetupTokenApproveRetrieveToken,\n                ";
}
echo "                onError: function (err) {\n                    WHMCS.payment.display.errorShow(err);\n                    scrollToGatewayInputError();\n                }\n\n            }).render(";
echo $element->buttonContainer();
echo ");\n        })();\n    })();\n}\n\n";
echo $module;
echo ".displayInstructions = function (inputContainer) {\n    let paymentInstructions = jQuery('.payment-instructions');\n    inputContainer.prepend(\n        paymentInstructions.detach()\n            .css('display', '')\n    );\n}\n\n";
echo $module;
echo ".updateNewPayMethodOption = function (newPayMethodOption) {\n    let inputContainer = jQuery(";
echo $element->container();
echo ");\n    let paymentInstructions = jQuery('.payment-instructions');\n    inputContainer.attr(\n        'data-";
echo $element->buttonContentAttribute;
echo "',\n        newPayMethodOption.contents()\n            .last()[0].textContent\n    );\n    newPayMethodOption.contents()\n        .last()[0].textContent = '';\n    if (inputContainer.data('";
echo $element->approvedAttribute;
echo "') == 1) {\n        newPayMethodOption.append(jQuery('.payer-label').clone());\n        paymentInstructions.show();\n        jQuery('.approval-instructions').show();\n    } else {\n        newPayMethodOption.append('&nbsp; ";
echo Lang::trans("paypalCommerce.linkAccount");
echo "');\n    }\n}\n\n";
echo $module;
echo ".restoreNewPayMethodOption = function (newPayMethodOption) {\n    let inputContainer = jQuery(";
echo $element->container();
echo ");\n    if (inputContainer.data('";
echo $element->approvedAttribute;
echo "') == 1) {\n        newPayMethodOption.find('.payer-label')\n            .remove();\n    } else {\n        newPayMethodOption.contents()\n            .last()[0].textContent = '';\n    }\n    newPayMethodOption.append(inputContainer.data('";
echo $element->buttonContentAttribute;
echo "'));\n    inputContainer.removeAttr('data-";
echo $element->buttonContentAttribute;
echo "');\n}\n\n";
echo $module;
echo ".updateCheckedOption = function (checkedOption)\n{\n    if (checkedOption.val() != 'new') {\n        jQuery(\n            '.paymethod-info[data-paymethod-id=\"' + checkedOption.val() + '\"]'\n        ).each(function () {\n            jQuery(this).addClass('selected');\n        });\n    } else {\n        checkedOption.closest('ul,.paymethod-info').addClass('selected');\n    }\n}\n\n";
echo $module;
echo ".updateUncheckedOption = function (uncheckedOption) {\n    if (uncheckedOption.val() != 'new') {\n        jQuery(\n            '.paymethod-info[data-paymethod-id=\"' + uncheckedOption.val() + '\"]'\n        ).each(function () {\n            jQuery(this).removeClass('selected');\n        });\n    } else {\n        uncheckedOption.closest('ul,.paymethod-info').removeClass('selected');\n    }\n}\n\n";
echo $module;
echo ".registerUI();\njQuery(document).ready(function() {\n        ((d, id) => {\n        if (d.getElementById(id)) {\n            return;\n        }\n        (d.getElementsByTagName('head')[0]).appendChild(";
echo $paypalSDK->renderTagAsScriptElement();
echo "(d, id));\n    })(document, '";
echo WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
echo "-paypal-sdk');\n\n    ";
if($renderSource == "invoice-pay") {
    echo "    (() => {\n        jQuery(";
    echo $element->container();
    echo ").hide();\n        jQuery('#newCCInfo')\n        .on('ifChecked.";
    echo $module;
    echo "', function () {\n            jQuery(";
    echo $element->container();
    echo ").slideDown();\n        })\n        .on('ifUnchecked.";
    echo $module;
    echo "', function () {\n            jQuery(";
    echo $element->container();
    echo ").slideUp();\n        });\n        jQuery('input[name=\"ccinfo\"]')\n        .on('ifChecked.";
    echo $module;
    echo "', function () {\n            ";
    echo $module;
    echo ".updateCheckedOption(jQuery(this));\n        })\n        .on('ifUnchecked.";
    echo $module;
    echo "', function () {\n            ";
    echo $module;
    echo ".updateUncheckedOption(jQuery(this));\n        });\n        jQuery('#frmPayment').addClass('";
    echo $element->paymentFormClass;
    echo "');\n        if (jQuery('#newCCInfo').is(':checked')) {\n            jQuery(";
    echo $element->container();
    echo ").show();\n        }\n        ";
    echo $module;
    echo ".updateNewPayMethodOption(\n            jQuery('.cc-payment-form .paymethod-info:not([data-paymethod-id]) label')\n        );\n        ";
    echo $module;
    echo ".displayInstructions(jQuery('#paymentGatewayInput'));\n    })();\n    ";
}
echo "    ";
echo $module;
echo ".updateCheckedOption(jQuery('.paypal_ppcpv-payment-form .checked input[name=\"ccinfo\"]'));\n});\n</script>\n";
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F76696577732F6A732F736D6172745F627574746F6E732D73646B2E7068703078376664353934323436356462_
{
    public $container;
    public $buttonContainer;
    public $payerContainer;
    public $paymentFormClass;
    public $approvedAttribute;
    public $buttonContentAttribute;
    public function __construct(string $module)
    {
        $this->container = $module . "_input_container";
        $this->buttonContainer = $this->container . "_button";
        $this->payerContainer = $this->container . "_payer";
        $this->paymentFormClass = $module . "-payment-form";
        $this->approvedAttribute = $module . "-approved";
        $this->buttonContentAttribute = $module . "-button-content";
    }
    public function asIdSelector($identifier)
    {
        return "'#" . $identifier . "'";
    }
    public function container()
    {
        return $this->asIdSelector($this->container);
    }
    public function buttonContainer()
    {
        return $this->asIdSelector($this->buttonContainer);
    }
    public function payerContainer()
    {
        return $this->asIdSelector($this->payerContainer);
    }
}

?>