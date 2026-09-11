@props(['statutActuel'])

@php
    $etapes = ['depose' => 'Déposé', 'en_transcription' => 'Transcription', 'en_relecture' => 'Relecture', 'en_validation' => 'Validation', 'restitue' => 'Restitué'];
    $ordre = array_keys($etapes);
    $indexActuel = array_search($statutActuel, $ordre) !== false ? array_search($statutActuel, $ordre) : 0;
    if (in_array($statutActuel, ['en_file', 'renvoye_correction'])) $indexActuel = array_search('depose', $ordre);
    if ($statutActuel === 'valide') $indexActuel = array_search('en_validation', $ordre);
@endphp

<div class="flex items-center w-full mb-8">
    @foreach ($etapes as $cle => $label)
        <div class="flex items-center {{ ! $loop->last ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center shrink-0">
                <div class="w-6 h-6 flex items-center justify-center text-[11px] font-mono border
                    {{ $loop->index <= $indexActuel ? 'bg-encre text-papier border-encre' : 'border-ligne text-meta' }}">
                    {{ $loop->index + 1 }}
                </div>
                <div class="text-[11px] mt-1.5 whitespace-nowrap {{ $loop->index <= $indexActuel ? 'text-encre' : 'text-meta' }}">
                    {{ $label }}
                </div>
            </div>
            @if (! $loop->last)
                <div class="flex-1 h-px mx-2 {{ $loop->index < $indexActuel ? 'bg-encre' : 'bg-ligne' }}"></div>
            @endif
        </div>
    @endforeach
</div>