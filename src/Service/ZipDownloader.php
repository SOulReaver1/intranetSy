<?php

namespace App\Service;

use App\Entity\CustomerFiles;
use App\Entity\Files;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class ZipDownloader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(CustomerFiles $customerFiles, array $files)
    {
        $zip = new ZipArchive;
        $fileName = md5($customerFiles->getId()).'.zip';
        $directory = $this->getTargetDirectory().'/'.$fileName;
        if($zip->open($directory, ZipArchive::CREATE) === true) {
            foreach ($files as $value) {
                $zip->addFile('uploads/files/'.$value->getFile(), $value->getFile());
            }
            $zip->close();
        }

        return $directory;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function delete(Files $file){
        $zip = new ZipArchive;
        $zip_name = md5($file->getCustomerFiles()->getId()).'.zip';
        $directory = $this->getTargetDirectory().'/'.$zip_name;
        if($zip->open($directory) === true) {
            $zip->deleteName($file->getFile());
            $zip->close();
            return true;
        }

        return false;
    }
}