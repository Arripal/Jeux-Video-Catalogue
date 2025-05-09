<?php

namespace App\Services;

use App\Entity\Player;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFile
{
    public function __construct(private string $target_directory, private SluggerInterface $slugger_interface) {}

    public function upload(UploadedFile $uploaded_file): string
    {
        $original_filename = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME);
        $safe_filename = $this->slugger_interface->slug($original_filename);
        $new_filename = $safe_filename . '-' . uniqid() . $uploaded_file->guessExtension();

        try {
            $uploaded_file->move($this->target_directory, $new_filename);
        } catch (FileException $e) {
            throw $e;
        }

        $file_path = $this->setFilePath($new_filename);

        return $file_path;
    }

    public function remove(string $file): void
    {
        $file_path = $this->setFilePath($file);

        if (!file_exists($file_path) or !is_file($file_path)) {
            throw new FileException("Le fichier demandé n'exsite pas, impossible de le supprimer.");
        }

        unlink($file_path);
    }


    private function setFilePath(string $filename): string
    {
        return $this->target_directory . '/' . $filename;
    }
}
