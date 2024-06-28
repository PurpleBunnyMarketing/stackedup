<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaPage extends Model
{
    use HasFactory;
    protected $table = 'media_pages';
    protected $fillable = [
        'custom_id', 'media_id', 'page_id', 'page_name', 'user_id', 'is_old', 'social_media_detail_id', 'image_url', 'google_mobile_number', 'account_properties'
    ];

    public function media()
    {
        return $this->belongsTo('App\Models\Media', 'media_id');
    }
    public function userMediaPages()
    {
        return $this->hasMany('App\Models\UserMedia', 'media_page_id');
    }

    public function postMedia()
    {
        return $this->hasOne('App\Models\PostMedia');
    }
    public function mediaPagesPayment()
    {
        return $this->hasMany('App\Models\UserMedia', 'media_page_id');
    }

    public function socialMediaDetail()
    {
        return $this->belongsTo('App\Models\SocialMediaDetail', 'social_media_detail_id');
    }
}
