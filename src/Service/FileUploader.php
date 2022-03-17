<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileUploader
{
    /**
     * Private Attributes
     */
    private string $targetDirectory;
    private AsciiSlugger $slugger;

    /**
     * Constructeur.
     *
     * @param string $targetDirectory Chemin dans lequel stocker l'image.
     */
    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = new AsciiSlugger();
    }

    /**
     * Upload de fichier sur serveur.
     *
     * @param UploadedFile $file
     * @return string Nom du fichier sauvegardé sur serveur.
     */
    public function upload(UploadedFile $file): string
    {
        // Initialisation du nom du fichier à sauvegarder sur serveur
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Upload du fichier
        $file->move($this->targetDirectory, $fileName);

        return $fileName;
    }
}