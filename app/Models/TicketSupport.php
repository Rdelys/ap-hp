<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSupport extends Model
{
    protected $table = 'tickets_support';

    protected $fillable = [
        'demandeur_id', 'demande_id', 'categorie', 'criticite',
        'sujet', 'description', 'statut', 'assigne_a_id', 'resolu_le',
    ];

    protected $casts = [
        'resolu_le' => 'datetime',
    ];

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    public function assigneA()
    {
        return $this->belongsTo(User::class, 'assigne_a_id');
    }

    public function messages()
    {
        return $this->hasMany(MessageTicket::class, 'ticket_id')->orderBy('created_at');
    }
}