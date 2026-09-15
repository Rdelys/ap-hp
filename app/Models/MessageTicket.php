<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTicket extends Model
{
    protected $table = 'messages_ticket';

    protected $fillable = ['ticket_id', 'auteur_id', 'message'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}