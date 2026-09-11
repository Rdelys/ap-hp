<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    public function index()
    {
        $etablissements = Etablissement::withCount('services')->orderBy('nom')->paginate(25);

        return view('admin.etablissements.index', compact('etablissements'));
    }

    public function create()
    {
        return view('admin.etablissements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:etablissements,code'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);

        Etablissement::create($validated);

        return redirect()->route('admin.etablissements.index')->with('succes', 'Établissement créé.');
    }

    public function edit(Etablissement $etablissement)
    {
        return view('admin.etablissements.edit', compact('etablissement'));
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:etablissements,code,'.$etablissement->id],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['sometimes', 'boolean'],
        ]);

        $validated['actif'] = $request->boolean('actif');

        $etablissement->update($validated);

        return redirect()->route('admin.etablissements.index')->with('succes', 'Établissement mis à jour.');
    }
}