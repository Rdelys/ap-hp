<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->actif;
    }

    public function rules(): array
    {
        return [
            'fichier_audio' => [
                'required',
                'file',
                'max:256000', // en Ko => 250 Mo
                'extensions:wav,mp3,dss,ds2', // dss/dss2 = formats Philips/Grundig courants en dictée médicale
            ],
            'type_document' => ['required', 'string', 'in:courrier,cr_hospitalisation,cr_operatoire,cr_consultation,autre'],
            'nom_demandeur' => ['nullable', 'string', 'max:255'],
            'numero_dictant' => ['nullable', 'string', 'max:50'],
            'niveau_urgence' => ['required', 'string', 'in:economique,normal,urgent'],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier_audio.required' => 'Le fichier audio est obligatoire.',
            'fichier_audio.max' => 'Le fichier audio ne doit pas dépasser 250 Mo.',
            'fichier_audio.extensions' => 'Formats acceptés : WAV, MP3, DSS, DS2.',
            'type_document.required' => 'Le type de document est obligatoire.',
            'niveau_urgence.required' => "Le niveau d'urgence est obligatoire.",
        ];
    }
}