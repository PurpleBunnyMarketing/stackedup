<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
   use HasFactory;

   protected $table = 'post_media';
   protected $fillable = [
      'custom_id', 'post_id', 'media_id', 'media_page_id', 'is_error', 'error_message'
   ];

   public function post()
   {
      return $this->belongsTo('App\Models\Post', 'post_id');
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
