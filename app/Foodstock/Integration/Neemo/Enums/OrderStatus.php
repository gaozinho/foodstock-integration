<?php

namespace App\Foodstock\Integration\Neemo\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    const Novo = 0;
    const Confirmado = 1;
    const Entregue = 2;
    const CanceladoRestaurante = 3;
    const Enviado = 4;
    const CanceladoAutomatico = 5;
    const CanceladoRestauranteEstornado = 6;
    const CanceladoAutomaticoEstornado = 7;

}
