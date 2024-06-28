<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMedia extends Model
{
    use HasFactory;

    protected $table = 'user_media';
    protected $fillable = [
        'custom_id', 'user_id', 'media_id', 'media_page_id','is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function media()
    {
        return $this->belongsTo('App\Models\Media', 'media_id');
    }
    public function mediaPage()
    {
        return $this->belongsTo('App\Models\MediaPage', 'media_page_id');
    }
}
