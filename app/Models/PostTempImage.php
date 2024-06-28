<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTempImage extends Model
{
    use HasFactory;

    protected $table = 'post_temp_images';
    protected $fillable = [ 
         'custom_id', 'upload_file','thumbnail','user_id','post_id'
    ];
    public function post()
    {
       return $this->belongsTo('App\Models\Post','post_id');
    }
    public function user()
    {
       return $this->belongsTo('App\User','user_id');
    }
}

