<?php

namespace App\Mail\Auth;

use App\Entity\User\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class VerifyMail extends Mailable
{
    use SerializesModels;

    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('andreii.kachanov@gmail.com')
            ->subject('Sign up Confirmation')
            ->markdown('emails.auth.register.verify', [
                'user' => $this->user
            ]);
    }
}
