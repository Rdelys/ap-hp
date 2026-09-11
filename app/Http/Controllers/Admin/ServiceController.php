<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('etablissement')->orderBy('nom')->paginate(25);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $etablissements = Etablissement::orderBy('nom')->get();

        return view('admin.services.create', compact('etablissements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'etablissement_id' => ['required', 'exists:etablissements,id'],
            'nom' => ['required', 'string', 'max:255'],
            'referent_nom' => ['nullable', 'string', 'max:255'],
            'referent_email' => ['nullable', 'email', 'max:255'],
            'referent_telephone' => ['nullable', 'string', 'max:30'],
            'regle_nommage' => ['nullable', 'string', 'max:255'],
            'ia_autorisee' => ['sometimes', 'boolean'],
        ]);

        $validated['ia_autorisee'] = $request->boolean('ia_autorisee');

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('succes', 'Service créé.');
    }

    public function edit(Service $service)
    {
        $etablissements = Etablissement::orderBy('nom')->get();

        return view('admin.services.edit', compact('service', 'etablissements'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'etablissement_id' => ['required', 'exists:etablissements,id'],
            'nom' => ['required', 'string', 'max:255'],
            'referent_nom' => ['nullable', 'string', 'max:255'],
            'referent_email' => ['nullable', 'email', 'max:255'],
            'referent_telephone' => ['nullable', 'string', 'max:30'],
            'regle_nommage' => ['nullable', 'string', 'max:255'],
            'ia_autorisee' => ['sometimes', 'boolean'],
            'actif' => ['sometimes', 'boolean'],
        ]);

        $validated['ia_autorisee'] = $request->boolean('ia_autorisee');
        $validated['actif'] = $request->boolean('actif');

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('succes', 'Service mis à jour.');
    }
}