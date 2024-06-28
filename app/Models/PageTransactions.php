<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTransactions extends Model
{
    use HasFactory;

    protected $table = 'page_transactions';
    protected $fillable = [
        'custom_id', 'user_id', 'payment_id', 'stripe_id', 'payment_status', 'type', 'amount'
    ];
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
