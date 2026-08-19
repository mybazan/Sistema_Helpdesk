<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Convert
{
    private $directorio;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->directorio = $parameterBag->get('kernel.project_dir');
    }

    public function imagenConvertidaBase64($path) {
        $path = $this->directorio . $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}