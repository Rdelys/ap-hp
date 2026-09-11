<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationCompte extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $lienInvitation) {}

    public function build()
    {
        return $this->subject('Création de votre compte — '.config('app.name'))
            ->view('emails.invitation');
    }
}