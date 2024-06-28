<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'packages';
    protected $fillable = [
        'custom_id', 'package_type', 'amount', 'description', 'product_id', 'price_id', 'is_active', 'actual_yearly_amount', 'yearly_off_amount'
    ];
}
