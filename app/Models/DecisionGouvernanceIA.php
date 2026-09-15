<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecisionGouvernanceIA extends Model
{
    protected $table = 'decisions_gouvernance_ia';

    protected $fillable = [
        'service_id', 'type_document', 'ia_autorisee', 'motif', 'decide_par_id',
    ];

    protected $casts = [
        'ia_autorisee' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function decidePar()
    {
        return $this->belongsTo(User::class, 'decide_par_id');
    }
}