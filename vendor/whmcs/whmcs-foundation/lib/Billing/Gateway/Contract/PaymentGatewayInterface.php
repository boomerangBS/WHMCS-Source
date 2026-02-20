<?php

namespace WHMCS\Billing\Gateway\Contract;

interface PaymentGatewayInterface extends IdentifierInterface, ViabilityInterface, PresentationInterface, CurrencyInterface, IntegrationInterface, LivecycleAwareInterface, SubscriptionInterface
{
}

?>