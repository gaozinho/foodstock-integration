<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $broker_id
 * @property string $merchant_id
 * @property string $authorizationCode
 * @property string $authorizationCodeVerifier
 * @property string $accessToken
 * @property string $refreshToken
 * @property string $expiresIn
 * @property string $created_at
 * @property string $updated_at
 * @property Broker $broker
 */
class IfoodBroker extends Model
{
    protected $table = 'federated_ifood_brokers';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['broker_id', 'merchant_id', 'restaurant_id', 'acknowledgment', 'validated', 'authorizationCode', 'authorizationCodeVerifier', 'accessToken', 'refreshToken', 'expiresIn', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function broker()
    {
        return $this->belongsTo('App\Models\Broker');
    }
}
