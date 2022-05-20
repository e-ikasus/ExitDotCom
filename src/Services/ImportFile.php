<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportFile
{
    private $file;
    private $storageLocation;
    private $slugger;
    private $newFileName;

    public function __construct($file, $storageLocation, SluggerInterface $slugger)
    {
        $this->file=$file;
        $this->storageLocation=$storageLocation;
        $this->slugger = $slugger;

    }

    public function storeFile()
    {
        $fileName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($fileName);
        $this->newFileName = $safeFilename . '-' . uniqid() . '.' . $this->file->guessExtension();

        // Move the file to the directory where covers are stored
        try {
            $this->file->move(
                $this->storageLocation,
                $this->newFileName
            );
        } catch
        (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }

    /**
     * @return mixed
     */
    public function getNewFileName(): String
    {
        return $this->newFileName;
    }


}