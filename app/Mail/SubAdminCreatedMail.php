<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubAdminCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data, $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $password)
    {
        $this->data = $data;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Account Created')->view('emails.admin.sub_admin_created', ['password' => $this->password, 'data' => $this->data]);
    }
}
