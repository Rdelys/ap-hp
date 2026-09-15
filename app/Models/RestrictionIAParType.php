<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictionIAParType extends Model
{
    protected $table = 'restrictions_ia_par_type';

    protected $fillable = ['service_id', 'type_document', 'ia_autorisee'];

    protected $casts = ['ia_autorisee' => 'boolean'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}