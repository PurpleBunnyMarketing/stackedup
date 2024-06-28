<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MediaPagePayments extends Model
{
    use HasFactory;

    protected $table = 'media_page_payments';
    protected $fillable = [
        'custom_id','user_id','media_id','payment_id','media_page_id','is_used','is_expiry'
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function media()
    {
        return $this->belongsTo('App\Models\Media', 'media_id');
    }
    public function payments()
    {
        return $this->belongsTo('App\Models\Payment', 'payment_id');
    }
    public function mediaPage()
    {
        return $this->belongsTo('App\Models\MediaPage', 'media_page_id');
    }

    // public function availableMediaPages() {
    //     return $this->mediaPages()->where('user_id',Auth::id());
    // }
}