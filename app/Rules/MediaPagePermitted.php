<?php

namespace App\Rules;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\UserMedia;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MediaPagePermitted implements Rule
{
    public $unpermitted_media_page = '';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $media_pages = explode(',', $value);
        $user = Auth::user();
        $flag = true;
        $user_permitted_media_page_ids = MediaPage::whereHas('userMediaPages', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('userMediaPages', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('custom_id')->toArray();
        foreach ($media_pages as $media_page) {
            if (!in_array($media_page, $user_permitted_media_page_ids)) {
                $this->unpermitted_media_page = $media_page;
                $flag = false;
            }
        }
        return $flag;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You don\'t have permission to add this page id - ' . $this->unpermitted_media_page;
    }
}
