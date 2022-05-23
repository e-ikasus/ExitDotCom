<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileDownloadController extends AbstractController
{
    /**
     * @Route("/download/{fileDirectory}/{fileName}", name="download_file", methods={"GET"})
     */
    public function downloadRessourcesFile(String $fileName, String $fileDirectory): BinaryFileResponse
    {
        return $this->file($this->getParameter($fileDirectory) . $fileName);
    }
}
