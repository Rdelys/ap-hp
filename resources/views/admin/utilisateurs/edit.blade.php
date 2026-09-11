@extends('layouts.app')

@section('titre', 'Modifier un utilisateur')
@section('sprint-actuel', 'Administration')

@section('contenu')
    <a href="{{ route('admin.utilisateurs.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Utilisateurs</a>
    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">{{ $utilisateur->name }}</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}" class="space-y-5 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Nom complet</label>
            <input type="text" name="name" value="{{ old('name', $utilisateur->name) }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $utilisateur->email) }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Rôle</label>
            <select name="role" required class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected($utilisateur->role->value === $role->value)>{{ $role->libelle() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Établissement</label>
            <select name="etablissement_id" class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="">—</option>
                @foreach ($etablissements as $e)<option value="{{ $e->id }}" @selected($utilisateur->etablissement_id === $e->id)>{{ $e->nom }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Service</label>
            <select name="service_id" class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="">—</option>
                @foreach ($services as $s)<option value="{{ $s->id }}" @selected($utilisateur->service_id === $s->id)>{{ $s->nom }}</option>@endforeach
            </select>
        </div>
        <button type="submit" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition">Enregistrer</button>
    </form>
@endsection