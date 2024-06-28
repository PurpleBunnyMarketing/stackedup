<?php

namespace App\Rules;

use App\Models\MediaPage;
use App\Models\UserMedia;
use Illuminate\Contracts\Validation\Rule;

class CheckUserPermittedMediaRule implements Rule
{
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
        $media_page = MediaPage::where('custom_id', $value)->first();
        if (!$media_page) return false;
        $isPermittedUserMedia = UserMedia::where('user_id', auth()->id())->where('media_page_id', $media_page->id)->first();
        if (!$isPermittedUserMedia) return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Selected media Page id is not valid. Please select a valid media Page id.';
    }
}
