<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookAds extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'request_type', 'request_filter', 'response_web', 'response_api'];
}
