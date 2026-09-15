<?php

namespace App\Services;

use App\Models\Demande;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateurDocumentWord
{
    public function generer(Demande $demande, string $texteValide): string
    {
        $service = $demande->service;
        $cheminModele = $service->modele_documentaire_path
            ? Storage::disk('documents_prives')->path($service->modele_documentaire_path)
            : null;

        $cheminTemporaire = storage_path('app/tmp_'.Str::uuid().'.docx');

        if ($cheminModele && file_exists($cheminModele)) {
            $this->genererDepuisModele($demande, $texteValide, $cheminModele, $cheminTemporaire);
        } else {
            $this->genererStandard($demande, $texteValide, $cheminTemporaire);
        }

        $nomFichier = $this->genererNomFichier($demande);
        $cheminFinal = 'restitutions/'.$demande->reference.'/'.$nomFichier;

        app(\App\Services\StockageChiffre::class)->ecrire($cheminFinal, file_get_contents($cheminTemporaire));
        unlink($cheminTemporaire);

        return $cheminFinal;
    }

    private function genererDepuisModele(Demande $demande, string $texte, string $cheminModele, string $sortie): void
    {
        $template = new TemplateProcessor($cheminModele);

        $template->setValue('etablissement', e($demande->etablissement->nom));
        $template->setValue('service', e($demande->service->nom));
        $template->setValue('type_document', e($demande->type_document));
        $template->setValue('nom_demandeur', e($demande->nom_demandeur ?? '—'));
        $template->setValue('numero_dictant', e($demande->numero_dictant ?? '—'));
        $template->setValue('reference', e($demande->reference));
        $template->setValue('date', now()->format('d/m/Y'));
        $template->setValue('contenu', e($texte));

        $template->saveAs($sortie);
    }

    private function genererStandard(Demande $demande, string $texte, string $sortie): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $styleTitre = ['bold' => true, 'size' => 14];
        $styleMeta = ['size' => 9, 'color' => '666666'];

        $section->addText(strtoupper($demande->type_document), $styleTitre);
        $section->addText($demande->etablissement->nom.' — '.$demande->service->nom, $styleMeta);
        $section->addTextBreak(1);

        $section->addText('Référence : '.$demande->reference, $styleMeta);
        $section->addText('Demandeur / dictant : '.($demande->nom_demandeur ?? '—'), $styleMeta);
        $section->addText('Numéro de dictant : '.($demande->numero_dictant ?? '—'), $styleMeta);
        $section->addText('Date de restitution : '.now()->format('d/m/Y H:i'), $styleMeta);
        $section->addTextBreak(2);

        foreach (explode("\n", $texte) as $paragraphe) {
            if (trim($paragraphe) !== '') {
                $section->addText($paragraphe);
            } else {
                $section->addTextBreak(1);
            }
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($sortie);
    }

    private function genererNomFichier(Demande $demande): string
    {
        $regle = $demande->service->regle_nommage;

        if (! $regle) {
            return $demande->reference.'_'.now()->format('Ymd_His').'.docx';
        }

        $nom = str_replace(
            ['{reference}', '{demandeur}', '{dictant}', '{date}', '{type}'],
            [
                $demande->reference,
                Str::slug($demande->nom_demandeur ?? 'inconnu'),
                $demande->numero_dictant ?? 'na',
                now()->format('Ymd'),
                $demande->type_document,
            ],
            $regle
        );

        return $nom.'.docx';
    }
}