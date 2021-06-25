<?php

namespace App\Foodstock\Integration\Ifood\Enums;

use BenSampo\Enum\Enum;

final class GrantType extends Enum
{
    const ClientCredentials = "client_credentials";
    const AuthorizationCode = "authorization_code";
    const RefreshToken = "refresh_token";
}
