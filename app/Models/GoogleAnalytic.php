<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class GoogleAnalytic extends Model
{
    use HasFactory;

    protected $table = 'google_analytics';
    protected $fillable = [
        'custom_id', 'user_id', 'request_type', 'request_filter', 'response_json', 'response_web', 'response_api'
    ];
}
