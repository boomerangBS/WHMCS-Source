<?php

namespace WHMCS\Order;

abstract class OrderPurchaseSource
{
    const ADMIN_MASQUERADING_AS_CLIENT = 1;
    const CLIENT = 2;
    const ADMIN = 3;
    const UNDEFINED = 4;
    const LOCAL_API = 5;
    const CLIENT_API = 6;
}

?>