<?php
namespace App\Foodstock\Integration\Ifood\RequestParameters;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class CancellationParameters{

    public $reason;
    public $cancellationCode;

    public function __construct($reason, $cancellationCode)
    {
        $this->reason = $reason;
        $this->cancellationCode = $cancellationCode;
    }

    public function toArray(){
        return [
            "reason" => $this->reason,
            "cancellationCode" => $this->cancellationCode,
        ];
    }

    public function toJson(){
        return json_encode($this);
    }

}