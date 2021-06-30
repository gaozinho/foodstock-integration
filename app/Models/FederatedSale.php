<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property float $restaurant_id
 * @property float $broker_id
 * @property string $brokerId
 * @property float $subtotal
 * @property float $amount
 * @property float $deliveryFee
 * @property float $orderAmount
 * @property int $shortOrderNumber
 * @property string $createdDate
 * @property int $ordersCountOnMerchant
 * @property string $customerName
 * @property string $deliveryFormattedAddress
 */
class FederatedSale extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['restaurant_id', 'broker_id', 'brokerId', 'subtotal', 'deliveryFee', 'orderAmount', 'shortOrderNumber', 'createdDate', 'ordersCountOnMerchant', 'customerName', 'deliveryFormattedAddress'];

}
