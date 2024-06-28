<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckSchedulePostTimeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $timezone;

    public function __construct()
    {
        $this->timezone = "Australia/Brisbane";
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
        $schedule_date = request('schedule_date');
        // dd($schedule_date, Carbon::now()->format('m/d/Y'), $value, Carbon::now()->format('g:i A'));
        if ($schedule_date == Carbon::now($this->timezone)->format('m/d/Y') && $value <= Carbon::now($this->timezone)->format('g:i A')) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Schedule time must be after the ' . Carbon::now($this->timezone)->format('g:i A');
    }
}
