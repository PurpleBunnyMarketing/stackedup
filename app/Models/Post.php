<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
        'custom_id', 'upload_file', 'thumbnail', 'hashtag', 'caption', 'schedule_date', 'user_id', 'schedule_time', 'is_active', 'schedule_date_time', 'is_call_to_action', 'call_to_action_type', 'action_link'
    ];
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function postMedia()
    {
        return $this->hasMany('App\Models\PostMedia');
    }

    public function postMediaList()
    {
        return $this->hasMany('App\Models\PostMedia', 'post_id');
    }

    // schedule_date_time
    public function getScheduleDateTimeAttribute($value)
    {
        //if call from the api so this is call
        if ($value == null) return null;

        if (request()->is("api/*")) {
            $timezone = request('timezone', "Australia/Brisbane");
            return convertUTCtoLocal($value, $timezone);
        } else {
            $timeZone = isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] : "Australia/Brisbane";
            return convertUTCtoLocal($value, $timeZone);
        }
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     $timeZone = isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] : "Australia/Brisbane";
    //     return convertUTCtoLocalDiffrentReturn($value, $timeZone);
    // }
    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('position');
    }
}
