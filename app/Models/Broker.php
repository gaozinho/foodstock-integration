<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $client_distributed_id
 * @property string $client_distributed_secret
 * @property string $created_at
 * @property string $updated_at
 * @property IfoodBroker[] $ifoodBrokers
 */
class Broker extends Model
{

    protected $table = 'federated_brokers';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'client_distributed_id', 'client_distributed_secret', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ifoodBrokers()
    {
        return $this->hasMany('App\Models\IfoodBroker');
    }
}
