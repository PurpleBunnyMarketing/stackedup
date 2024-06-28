<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTempImage extends Model
{
     use HasFactory;

    protected $table = 'staff_temp_images';
    protected $fillable = [ 
         'custom_id', 'profile_photo','user_id'
    ];
    public function user()
    {
       return $this->belongsTo('App\User','user_id');
    }
}
