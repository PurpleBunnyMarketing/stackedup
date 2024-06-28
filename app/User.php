<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getRouteKeyName()
    {
        return 'custom_id';
    }
    protected $table = 'users';
    protected $fillable = [
        'custom_id', 'customer_id', 'parent_id', 'full_name', 'email', 'phone_code', 'mobile_no', 'contact_no', 'profile_photo', 'otp', 'password', 'type', 'is_active', 'is_subscribe', 'last_loggedIn', 'company_name', 'abn', 'company_address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function parents()
    {
        return $this->hasMany('App\User', 'parent_id');
    }
    public function company()
    {
        return $this->belongsTo('App\User', 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo('App\Models\Video', 'video_id');
    }
    public function media()
    {
        return $this->hasMany('App\Models\UserMedia', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'user_id');
    }
    public function socialMediaDetail()
    {
        return $this->hasMany('App\Models\SocialMediaDetail', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'user_id');
    }


    public function mediaPageCount()
    {
        return $this->hasMany('App\Models\MediaPagePayments', 'user_id');
    }


    public function facebookPages()
    {

        return $this->hasMany('App\Models\UserMedia', 'user_id')->where('media_id', 1)->where('user_id', Auth::id())->where('is_deleted', 'n');
    }
    public function linkedinPages()
    {

        return $this->hasMany('App\Models\UserMedia', 'user_id')->where('media_id', 2)->where('user_id', Auth::id())->where('is_deleted', 'n');
    }
    public function twitterPages()
    {
        return $this->hasMany('App\Models\UserMedia', 'user_id')->where('media_id', 3)->where('user_id', Auth::id())->where('is_deleted', 'n');
    }

    public function Analytic()
    {
        return $this->hasOne('App\Models\Analytics', 'user_id');
    }

    public function parentUser(){
        return $this->belongsTo(self::class , 'parent_id');
    }
}
