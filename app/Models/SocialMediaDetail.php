<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaDetail extends Model
{
   use HasFactory;
   protected $table = 'social_media_details';
   protected $fillable = [
      'custom_id', 'user_id', 'media_id', 'social_id', 'token', 'token_secret', 'token_expiry', 'token_expiry_time', 'refresh_token'
   ];
   public function user()
   {
      return $this->belongsTo('App\User', 'user_id');
   }
   public function media()
   {
      return $this->belongsTo('App\Models\Media', 'media_id');
   }
}
