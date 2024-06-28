<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramAnalytics extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'request_type', 'request_filter', 'response_web', 'response_api', 'media_page_id', 'day_data', 'month_data', 'month_year'];

}
