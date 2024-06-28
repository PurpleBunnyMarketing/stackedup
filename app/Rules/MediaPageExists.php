<?php

namespace App\Rules;

use App\Models\MediaPage;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class MediaPageExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $media_page_ids = MediaPage::pluck('custom_id')->toArray();
        $keywords = explode(',', $value);
        $intersect = array_intersect($keywords, $media_page_ids);
        if (count($keywords) != count($intersect)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected media page id is invalid.';
    }
}
