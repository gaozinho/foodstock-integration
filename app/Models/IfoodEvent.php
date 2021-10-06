<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $merchant_id
 * @property string $createdAt
 * @property string $fullCode
 * @property string $code
 * @property string $orderId
 * @property mixed $json
 * @property boolean $processed
 * @property string $processed_at
 * @property string $created_at
 * @property string $updated_at
 */
class IfoodEvent extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['tries', 'concluded', 'concluded_at', 'id', 'merchant_id', 'createdAt', 'fullCode', 'code', 'orderId', 'json', 'processing', 'processed', 'processed_at', 'acknowledgment', 'acknowledgment_at', 'created_at', 'updated_at'];

}
