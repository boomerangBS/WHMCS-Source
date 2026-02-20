<?php

$element = new func_num_args($module);
echo "\n";
if($renderSource == "checkout") {
    echo "\n<div id=\"";
    echo $element->container;
    echo "\" class=\"row\" style=\"display:none;\">\n    <div class=\"col-md-6\">\n        <div id=\"";
    echo $element->fieldCardNumber;
    echo "\"></div>\n    </div>\n    <div class=\"col-md-3\">\n        <div id=\"";
    echo $element->fieldExpiry;
    echo "\"></div>\n    </div>\n    <div class=\"col-md-3\">\n        <div id=\"";
    echo $element->fieldSecurityCode;
    echo "\"></div>\n    </div>\n</div>\n\n";
} elseif(in_array($renderSource, ["invoice-pay", "payment-method-add"])) {
    echo "\n<div id=\"";
    echo $element->container;
    echo "\" style=\"display:none;\">\n    <div class=\"row\">\n        <label for=\"";
    echo $element->fieldCardNumber;
    echo "\" class=\"col-sm-4 control-label\"\n            >";
    echo Lang::trans("creditcardcardnumber");
    echo "</label>\n        <div class=\"col-sm-7\">\n            <div id=\"";
    echo $element->fieldCardNumber;
    echo "\" aria-describedby=\"cc-type\"></div>\n        </div>\n        <div class=\"col-sm-4\"></div>\n    </div>\n    <div class=\"row\">\n        <label for=\"";
    echo $element->fieldExpiry;
    echo "\" class=\"col-sm-4 control-label\"\n            >";
    echo Lang::trans("creditcardcardexpires");
    echo "</label>\n        <div class=\"col-sm-3\">\n            <div id=\"";
    echo $element->fieldExpiry;
    echo "\"></div>\n        </div>\n        <div class=\"col-sm-6\"></div>\n    </div>\n    <div class=\"row\">\n        <label for=\"";
    echo $element->fieldSecurityCode;
    echo "\" class=\"col-sm-4 control-label\"\n            >";
    echo Lang::trans("creditcardcvvnumbershort");
    echo "</label>\n        <div class=\"col-sm-3\">\n            <div id=\"";
    echo $element->fieldSecurityCode;
    echo "\"></div>\n        </div>\n    </div>\n</div>\n\n";
}
echo "\n<script type=\"text/javascript\">\nlet ";
echo $module;
echo " = {\n    translations: {\n        cardDeclined: '";
echo addslashes(Lang::trans("creditcarddeclined"));
echo "',\n        cardFieldsErrors: new Map([\n            ['INVALID_NUMBER', '";
echo addslashes(Lang::trans("creditcardnumberinvalid"));
echo "'],\n            ['INVALID_CVV', '";
echo addslashes(Lang::trans("creditcardccvinvalid"));
echo "'],\n            ['INVALID_EXPIRY', '";
echo addslashes(Lang::trans("creditcardexpirydateinvalid"));
echo "']\n        ])\n    },\n    cardFields: null,\n    cardFieldsUnavailable: false,\n    orderIdentifier: null,\n    setupToken: null,\n    cardFieldsStyle: {\n        'input': {\n            'font-size': '1rem',\n            'font-family': 'sans-serif',\n            'font-weight': '300',\n            'font-color': '#626262',\n            'line-height': '1.5',\n            'padding': '9px 12px',\n        },\n    },\n};\n\n";
echo $module;
echo ".showUnavailable = function (renderSource) {\n    WHMCS.payment.display.submitDisable(renderSource);\n    WHMCS.payment.display.errorShow('";
echo addslashes(Lang::trans("unavailable"));
echo "');\n};\n\n";
echo $module;
echo ".clearUnavailable = function (renderSource) {\n    ";
echo $module;
echo ".cardFieldsUnavailable = false;\n    WHMCS.payment.display.submitReset(renderSource);\n    WHMCS.payment.display.errorClear();\n}\n\n";
echo $module;
echo ".hasSavePayment = function () {\n    let pstore = jQuery(";
echo $element->fieldSaveCard();
echo ");\n    return (pstore.length == 0 || pstore.is(':checked'));\n}\n\n";
if($renderSource == "checkout") {
    echo "\n";
    echo $module;
    echo ".registerUI = function () {\n    let toggleSelectors = '#newCardInfo,#existingCardInfo';\n    let paymentForm = jQuery('#frmCheckout');\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewayInit(function (metadata, element) {\n            jQuery(";
    echo $element->container();
    echo ").insertAfter(jQuery('#newCardInfo'));\n        })\n        .onGatewaySelected(function (metadata, element) {\n            jQuery('#new')\n                .on('ifChecked.";
    echo $module;
    echo "', function () {\n                    jQuery(toggleSelectors).hide();\n                    jQuery(";
    echo $element->container();
    echo ").slideDown();\n                })\n                .on('ifUnchecked.";
    echo $module;
    echo "', function () {\n                    jQuery(";
    echo $element->container();
    echo ").slideUp();\n                });\n            jQuery(toggleSelectors).hide();\n            if (jQuery('#new').is(':checked')) {\n                jQuery(";
    echo $element->container();
    echo ").show();\n            }\n                        if (";
    echo $module;
    echo ".cardFields != null && !";
    echo $module;
    echo ".cardFields.isEligible()) {\n                ";
    echo $module;
    echo ".cardFieldsUnavailable = true;\n                ";
    echo $module;
    echo ".showUnavailable(metadata._source);\n            }\n            WHMCS.payment.behavior.disableDefaultCardValidation(metadata._source);\n            ";
    if($showSaveToggle) {
        echo "                jQuery('#inputNoStoreContainer').show();\n            ";
    }
    echo "        })\n        .onGatewayUnselected(function (metadata, element) {\n            jQuery('#new').off('ifChecked.";
    echo $module;
    echo "')\n                .off('ifUnchecked.";
    echo $module;
    echo "');\n            jQuery(toggleSelectors).show();\n            jQuery(";
    echo $element->container();
    echo ").hide();\n            WHMCS.payment.display.errorClear();\n            if (";
    echo $module;
    echo ".cardFieldsUnavailable) {\n                ";
    echo $module;
    echo ".clearUnavailable(metadata._source);\n            }\n            WHMCS.payment.behavior.enableDefaultCardValidation(metadata._source);\n        })\n        .onCheckoutFormSubmit(function (metadata, element) {\n            WHMCS.payment.display.errorClear();\n                        if (";
    echo $module;
    echo ".orderIdentifier != null) {\n                return;\n            }\n                        if (";
    echo $module;
    echo ".setupToken != null) {\n                return;\n            }\n            if (!jQuery('#new').is(':checked')) {\n                return;\n            }\n            metadata.event.preventDefault();\n            ";
    echo $module;
    echo ".cardFields.submit({})\n                .then(function () {\n                    jQuery('#btnCompleteOrder').click();\n                })\n                .catch(function (err) {\n                    ";
    echo $module;
    echo ".orderIdentifier = null;\n                    ";
    echo $module;
    echo ".setupToken = null;\n                    ";
    echo $module;
    echo ".handleError(err, metadata._source);\n                });\n        });\n};\n\n";
    echo $module;
    echo ".initCardFields = function () {\n    let identityFields = function () {\n        return {\n            firstName: jQuery('#inputFirstName').val(),\n            lastName: jQuery('#inputLastName').val(),\n            email: jQuery('#inputCompanyName').val(),\n            address1: jQuery('#inputAddress1').val(),\n            address2: jQuery('#inputAddress2').val(),\n            city: jQuery('#inputCity').val(),\n            state: jQuery('#frmCheckout [name=\"state\"]:input').val(),\n            postcode: jQuery('#inputPostcode').val(),\n            country: jQuery('#inputCountry').val(),\n            phoneNumber: jQuery('#inputPhone').val(),\n            billingcontact: null,\n        };\n    }\n    ";
    if($requiresPayment) {
        echo "    return ";
        echo $module;
        echo ".initCardFieldsPayment(\n        function () {\n            return {\n                ...{\n                    invoiceid: null,\n                    custtype: jQuery('#inputCustType').val(),\n                    vaultCard: ";
        echo $module;
        echo ".hasSavePayment(),\n                },\n                ...(identityFields()),\n            };\n        },\n        function (orderIdentifier) {}\n    );\n    ";
    } else {
        echo "    return ";
        echo $module;
        echo ".initCardFieldsSetup(\n        function () {\n            return {\n                ...{\n                    custtype: jQuery('#inputCustType').val(),\n                    vaultCard: ";
        echo $module;
        echo ".hasSavePayment(),\n                },\n                ...(identityFields()),\n            };\n        },\n        function () {}\n    );\n\n    ";
    }
    echo "};\n\n";
} elseif($renderSource == "invoice-pay") {
    echo "\n";
    echo $module;
    echo ".registerUI = function () {\n    let paymentForm = jQuery('#frmPayment');\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewayInit(function (metadata, element) {\n            let ccForm = jQuery('div.cc-payment-form');\n            let invoiceCCElements = jQuery('#inputCardCvv').closest('div.form-group', ccForm)\n                .add(jQuery('#inputCardNumber').closest('div.form-group', ccForm))\n                .add(jQuery('#inputCardExpiry').closest('div.form-group', ccForm));\n            let container = jQuery(";
    echo $element->container();
    echo ");\n            let newCcInput = jQuery('#newCCInfo');\n            WHMCS.payment.behavior.disableDefaultCardValidation(metadata._source);\n            newCcInput\n                .on('ifChecked.";
    echo $module;
    echo "', function (event) {\n                    invoiceCCElements.hide();\n                    container.slideDown();\n                })\n                .on('ifUnchecked.";
    echo $module;
    echo "', function (event) {\n                    container.slideUp();\n                });\n            container.insertBefore(\n                paymentForm.find('#billingAddressChoice')\n            );\n\n            invoiceCCElements.hide();\n            if (newCcInput.is(\":checked\")) {\n                container.show();\n            }\n        })\n        .onCheckoutFormSubmit(function (metadata, element) {\n            WHMCS.payment.display.errorClear();\n                        if (";
    echo $module;
    echo ".orderIdentifier != null) {\n                return;\n            }\n            if (!jQuery('#newCCInfo').is(':checked')) {\n                return;\n            }\n            metadata.event.preventDefault();\n            ";
    echo $module;
    echo ".cardFields.submit({})\n                .catch(function (err) {\n                    ";
    echo $module;
    echo ".orderIdentifier = null;\n                    ";
    echo $module;
    echo ".handleError(err, metadata._source);\n                    scrollToGatewayInputError();\n                });\n        });\n};\n\n";
    echo $module;
    echo ".initCardFields = function () {\n    let identityFields = function () {\n        return {\n            firstName: jQuery('#inputFirstName').val(),\n            lastName: jQuery('#inputLastName').val(),\n            email: jQuery('#inputCompanyName').val(),\n            address1: jQuery('#inputAddress1').val(),\n            address2: jQuery('#inputAddress2').val(),\n            city: jQuery('#inputCity').val(),\n            state: jQuery('#frmPayment [name=\"state\"]:input').val(),\n            postcode: jQuery('#inputPostcode').val(),\n            country: jQuery('#country').val(),\n            phoneNumber: jQuery('#inputPhone').val(),\n            billingcontact: jQuery('input[name=\"billingcontact\"]:checked').val(),\n        };\n    }\n    let invoiceId = function () {\n        return jQuery('input[name=\"invoiceid\"]').val();\n    }\n    return ";
    echo $module;
    echo ".initCardFieldsPayment(\n        function () {\n            return {\n                ...{\n                    invoiceid: invoiceId(),\n                    vaultCard: ";
    echo $module;
    echo ".hasSavePayment(),\n                },\n                ...(identityFields()),\n            };\n        },\n        function (orderIdentifier) {\n            return new Promise(function (resolve, reject) {\n                let formData = jQuery('#frmPayment').serializeArray();\n                formData.push({name: 'orderid', value: orderIdentifier});\n                WHMCS.http.jqClient.jsonPost({\n                    url: '";
    echo $routeInvoiceOnApprove;
    echo "',\n                    data: formData,\n                    success: function (response) {\n                        resolve(response);\n                    },\n                    error: function (response) {\n                        reject(response);\n                    }\n                });\n            })\n            .then(function (data) {\n                if (data.success) {\n                    if (data.redirectUrl) {\n                        window.location = data.redirectUrl;\n                    } else {\n                        window.location.reload();\n                    }\n                } else {\n                    if (data.status === 'declined') {\n                        throw new Error(paypal_acdc.translations.cardDeclined);\n                    }\n                    throw new Error(data.error);\n                }\n            })\n            .catch(function (err) {\n                ";
    echo $module;
    echo ".orderIdentifier = null;\n                ";
    echo $module;
    echo ".handleError(err, '";
    echo $renderSource;
    echo "');\n            });\n        }\n    );\n};\n\n";
} elseif($renderSource == "payment-method-add") {
    echo "\n";
    echo $module;
    echo ".registerUI = function () {\n    WHMCS.payment.handler.make('";
    echo $module;
    echo "')\n        .onGatewaySelected(function (metadata, element) {\n            jQuery('div.fieldgroup-creditcard').hide();\n            WHMCS.payment.display.show(jQuery(";
    echo $element->container();
    echo "));\n        })\n        .onGatewayUnselected(function (metadata, element) {\n            jQuery(";
    echo $element->container();
    echo ").remove();\n        })\n        .onAddPayMethodFormSubmit(function (metadata, element) {\n            metadata.event.preventDefault();\n            ";
    echo $module;
    echo ".cardFields.submit({})\n                .catch(function (err) {\n                    ";
    echo $module;
    echo ".handleError(err, metadata._source);\n                });\n        });\n}\n\n";
    echo $module;
    echo ".initCardFields = function () {\n    return ";
    echo $module;
    echo ".initCardFieldsSetup(\n        function () {\n            return {\n                ...{\n                    token: csrfToken,\n                    billingcontact: jQuery('input[name=\"billingcontact\"]:checked').val()\n                },\n            };\n        },\n        function ({vaultSetupToken, liabilityShift}) {\n            ";
    echo $module;
    echo ".checkLiabilityShift(liabilityShift);\n            return new Promise(function (resolve, reject) {\n                WHMCS.http.jqClient.jsonPost({\n                    url: '";
    echo $routeCreatePaymentToken;
    echo "',\n                    data: {\n                        token: csrfToken,\n                        setuptoken: vaultSetupToken,\n                        description: jQuery('#inputDescription').val(),\n                        billingcontact: jQuery('input[name=\"billingcontact\"]:checked').val()\n                    },\n                    success: function (response) {\n                        resolve(response);\n                    },\n                    error: function (response) {\n                        reject(response);\n                    }\n                });\n            }).then(function () {\n                window.location.replace('";
    echo $routePaymentMethods;
    echo "');\n            });\n        }\n    );\n}\n\n";
} else {
    echo $module;
    echo ".registerUI = function () {};\n";
    echo $module;
    echo ".initCardFields = function () {};\n";
}
echo "\n";
echo $module;
echo ".handleError = function (err, renderSource) {\n    let displayError = '';\n    if (err.message) {\n        displayError += ";
echo $module;
echo ".translateError(err.message);\n    } else if (err.Error) {\n        displayError += err.Error;\n    } else if (typeof err == 'string') {\n        displayError = err;\n    } else {\n        displayError = 'PayPal Capture: Unknown Error.';\n    }\n    WHMCS.payment.display.errorShow(displayError);\n    WHMCS.payment.display.submitReset(renderSource);\n}\n\n";
echo $module;
echo ".translateError = function (code) {\n    if (";
echo $module;
echo ".translations.cardFieldsErrors.has(code)) {\n        return ";
echo $module;
echo ".translations.cardFieldsErrors.get(code);\n    }\n    return code;\n}\n\n";
echo $module;
echo ".initCardFieldsSetup = function (createSetupTokenFields, onApprove) {\n    return paypal.CardFields({\n        style: ";
echo $module;
echo ".cardFieldsStyle,\n        createVaultSetupToken: function () {\n            return new Promise(function (resolve, reject) {\n                WHMCS.http.jqClient.jsonPost({\n                    url: '";
echo $routeCreateSetupToken;
echo "',\n                    data: {\n                        token: csrfToken,\n                        ...createSetupTokenFields()\n                    },\n                    success: function (response) {\n                        resolve(response);\n                    },\n                    error: function (response) {\n                        reject(response);\n                    }\n                });\n            }).then(function (response) {\n                return response.id;\n            });\n        },\n        onApprove: function (data) {\n            ";
echo $module;
echo ".setupToken = data.vaultSetupToken;\n            onApprove(data);\n        },\n        onError: function (err) {\n            throw new Error(err);\n        }\n    });\n}\n";
echo $module;
echo ".initCardFieldsPayment = function (createOrderFields, onApprove) {\n    return paypal.CardFields({\n        style: ";
echo $module;
echo ".cardFieldsStyle,\n        createOrder: function () {\n            return fetch('";
echo $routeCreateOrder;
echo "', {\n                method: 'post',\n                headers: {\n                    'content-type': 'application/json'\n                },\n                body: JSON.stringify({\n                    ...{token: csrfToken},\n                    ...createOrderFields(),\n                })\n            }).then(function (res) {\n                return res.json();\n            }).then(function (data) {\n                return data.id;\n            });\n        },\n        onApprove: function (data) {\n            const {liabilityShift, orderID} = data;\n            ";
echo $module;
echo ".checkLiabilityShift(liabilityShift);\n            ";
echo $module;
echo ".orderIdentifier = orderID;\n            onApprove(";
echo $module;
echo ".orderIdentifier);\n        },\n        onError: function (err) {\n            throw new Error(err);\n        }\n    });\n}\n\n";
echo $module;
echo ".renderCardFields = function () {\n        return (async() => {\n        while (typeof paypal === 'undefined') {\n            await new Promise(resolve => setTimeout(resolve, 250));\n        }\n                let ineligibleOnLoad = true;\n        ";
if($renderSource == "checkout") {
    echo "        ineligibleOnLoad = WHMCS.payment.query.isGatewaySelected('";
    echo $module;
    echo "');\n        ";
}
echo "        ";
echo $module;
echo ".cardFields = ";
echo $module;
echo ".initCardFields();\n\n        if (";
echo $module;
echo ".cardFields != null && ";
echo $module;
echo ".cardFields.isEligible()) {\n            ";
echo $module;
echo ".cardFields.NumberField().render(";
echo $element->fieldCardNumber();
echo ");\n            ";
echo $module;
echo ".cardFields.ExpiryField().render(";
echo $element->fieldExpiry();
echo ");\n            ";
echo $module;
echo ".cardFields.CVVField().render(";
echo $element->fieldSecurityCode();
echo ");\n        } else if (ineligibleOnLoad) {\n            ";
echo $module;
echo ".cardFieldsUnavailable = true;\n            ";
echo $module;
echo ".showUnavailable('";
echo $renderSource;
echo "');\n        }\n    })();\n}\n\n";
echo $module;
echo ".checkLiabilityShift = function (liabilityShift) {\n    if (liabilityShift && (liabilityShift !== 'POSSIBLE' && liabilityShift !== 'YES')) {\n        throw new Error(paypal_acdc.translations.cardDeclined);\n    }\n}\n\n";
echo $module;
echo ".registerUI();\njQuery(document).ready(function() {\n        ((d, id) => {\n        if (d.getElementById(id)) {\n            return;\n        }\n        (d.getElementsByTagName('head')[0]).appendChild(";
echo $paypalSDK->renderTagAsScriptElement();
echo "(d, id));\n    })(document, '";
echo WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
echo "-paypal-sdk');\n\n    ";
echo $module;
echo ".renderCardFields();\n});\n</script>\n\n";
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F616364632F76696577732F6A732F6A732D73646B2E7068703078376664353934323435363538_
{
    public $container;
    public $fieldCardNumber;
    public $fieldExpiry;
    public $fieldSecurityCode;
    public $fieldSaveCard;
    public function __construct(string $module)
    {
        $this->container = $module . "_input_container";
        $this->fieldCardNumber = $module . "-CreditCard";
        $this->fieldExpiry = $module . "-ExpiryDate";
        $this->fieldSecurityCode = $module . "-Cvc";
        $this->fieldSaveCard = "inputNoStore";
    }
    public function asIdSelector($identifier)
    {
        return "'#" . $identifier . "'";
    }
    public function container()
    {
        return $this->asIdSelector($this->container);
    }
    public function fieldCardNumber()
    {
        return $this->asIdSelector($this->fieldCardNumber);
    }
    public function fieldExpiry()
    {
        return $this->asIdSelector($this->fieldExpiry);
    }
    public function fieldSecurityCode()
    {
        return $this->asIdSelector($this->fieldSecurityCode);
    }
    public function fieldSaveCard()
    {
        return $this->asIdSelector($this->fieldSaveCard);
    }
}

?>