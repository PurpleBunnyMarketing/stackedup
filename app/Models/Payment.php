<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'custom_id', 'user_id', 'payment_id', 'subscription_id', 'price_id', 'start_date', 'end_date', 'payment_status', 'type', 'amount', 'coupon_used', 'discount_amount', 'used_coupon_name'
    ];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['end_date'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function plan()
    {
        return $this->hasMany('App\Models\Package');
    }
    public function mediaPageCount()
    {
        return $this->hasMany('App\Models\MediaPagePayments');
    }
}
