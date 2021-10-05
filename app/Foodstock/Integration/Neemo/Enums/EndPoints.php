<?php

namespace App\Foodstock\Integration\Neemo\Enums;

use BenSampo\Enum\Enum;

final class EndPoints extends Enum
{
    const Base = "https://deliveryapp.neemo.com.br";
    //const UserCode = "/authentication/v1.0/oauth/userCode";
    //const Token = "/authentication/v1.0/oauth/token";
    const EventsPooling = "/api/integration/v1/order"; //Post
    const EventsAcknowledgment = "/api/integration/v1/order/%s"; //Put
    const OrderDetail = "/api/integration/v1/order/%s"; //Post


    //const OrderActionConfirm = "/order/v1.0/orders/%s/confirm";
    //const OrderActionReadyToPickup = "/order/v1.0/orders/%s/readyToPickup";
    //const OrderActionDispatch = "/order/v1.0/orders/%s/dispatch";
    //const OrderActionRequestCancellation = "/order/v1.0/orders/%s/requestCancellation";
    //const OrderActionAcceptCancellation = "/order/v1.0/orders/%s/acceptCancellation";
    //const OrderActionDenyCancellation = "/order/v1.0/orders/%s/denyCancellation";

}
