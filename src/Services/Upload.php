<?php

namespace App\Services;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload
{
    private $directorioRaiz;
    private $fichaDirectorio = '/Ficha';
    private $maxImgSize = 1000*1000*5;
    private $authType = ["jpeg","jpg","png"];

    public function validateImg(UploadedFile $image){

        if ($image->getSize()>$this->maxImgSize)
            return false;
        else if (!in_array($image->guessExtension(),$this->authType)){
            return false;
        }
        return true;
    }

    private function subir(UploadedFile $archivo, string $directorioOperacion) : ?string {
        $nombreFinalArchivo = md5(uniqid()) . "-" . uniqid() . '.' . $archivo->guessExtension();
        $archivo->move($directorioOperacion, $nombreFinalArchivo);
        return $nombreFinalArchivo;
    }

    public function subirDocumentoPDF(UploadedFile $documento, string $directorio) : ?string{
        return $this->subir($documento, $directorio);
    }
}