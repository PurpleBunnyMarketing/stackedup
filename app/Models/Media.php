<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';
    protected $fillable = [
        'custom_id', 'name', 'image_url', 'website_image_url', 'order_sequence'
    ];

    public function user_media()
    {
        return $this->hasMany('App\Models\UserMedia', 'media_id');
    }
    public function postMedia()
    {
        return $this->hasMany('App\Models\PostMedia', 'media_id');
    }
    public function mediaPages()
    {
        return $this->hasMany('App\Models\MediaPage', 'media_id');
    }
    public function socialMediaDetails()
    {
        return $this->hasMany('App\Models\SocialMediaDetail', 'media_id');
    }
}
