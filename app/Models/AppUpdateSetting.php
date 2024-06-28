<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUpdateSetting extends Model
{
    use HasFactory;
    //fillable
    protected $fillable = [
        'slug',
        'build_version',
        'app_version',
        'is_force_update',
    ];
}
