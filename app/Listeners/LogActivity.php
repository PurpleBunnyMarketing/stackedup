<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events as LaravelEvents;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\User;

class LogActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        //
    }


    public function login(LaravelEvents\Login $event)
    {
        $logindatetime = Carbon::now();

        User::where('id', $event->user->id)->update(
            [
                'last_loggedIn' => $logindatetime
            ]
        );
        $this->info($event, "User {$event->user->email} logged in at {$logindatetime}", $event->user->only('id', 'email'));
    }

    protected function info(object $event, string $message, array $context = [])
    {
        //$class = class_basename($event::class);
        $class = get_class($event);

        Log::info("[{$class}] {$message}", $context);
    }
}
