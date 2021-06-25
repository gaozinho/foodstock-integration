<?php

namespace App\Foodstock\Integration\Ifood\Enums;

use BenSampo\Enum\Enum;

final class EndPoints extends Enum
{
    const Base = "https://merchant-api.ifood.com.br";
    const UserCode = "/authentication/v1.0/oauth/userCode";
    const Token = "/authentication/v1.0/oauth/token";
    const EventsPooling = "/order/v1.0/events:polling";
    const EventsAcknowledgment = "/order/v1.0/events/acknowledgment";
    const OrderDetail = "/order/v1.0/orders/";


    const OrderActionConfirm = "/order/v1.0/orders/%s/confirm";
    const OrderActionReadyToPickup = "/order/v1.0/orders/%s/readyToPickup";
    const OrderActionDispatch = "/order/v1.0/orders/%s/dispatch";
    const OrderActionRequestCancellation = "/order/v1.0/orders/%s/requestCancellation";
    const OrderActionAcceptCancellation = "/order/v1.0/orders/%s/acceptCancellation";
    const OrderActionDenyCancellation = "/order/v1.0/orders/%s/denyCancellation";

}
