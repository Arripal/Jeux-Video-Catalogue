<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFile
{

    private $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(private string $target_directory, private SluggerInterface $slugger_interface) {}

    public function upload(UploadedFile $uploaded_file): string
    {

        $this->verifyMimeType($uploaded_file);

        $original_filename = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME);
        $safe_filename = $this->slugger_interface->slug($original_filename);
        $extension = $uploaded_file->guessExtension() ?? $uploaded_file->getClientOriginalExtension();
        $new_filename = $safe_filename . '-' . uniqid() . '.' . $extension;

        $uploaded_file->move($this->target_directory, $new_filename);

        return $new_filename;
    }

    public function remove(string $file): void
    {
        $file_path = $this->setFilePath($file);

        if (!file_exists($file_path) or !is_file($file_path)) {
            throw new FileException("Le fichier spécifié n'existe pas ou n'est pas un fichier valide.");
        }

        unlink($file_path);
    }

    private function setFilePath(string $filename): string
    {
        return $this->target_directory . '/' . $filename;
    }

    private function verifyMimeType(?UploadedFile $uploaded_file)
    {
        if (!in_array($uploaded_file->getMimeType(), $this->allowed_mime_types)) {
            throw new FileException("Seules les images sont autorisées (jpeg, png, webp).");
        }
    }
}
