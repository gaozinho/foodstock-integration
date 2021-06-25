<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IfoodBroker extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'broker_id' => $this->broker_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
