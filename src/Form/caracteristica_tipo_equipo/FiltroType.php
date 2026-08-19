<?php

namespace App\Form\caracteristica_tipo_equipo;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
* Esta clase define los campos que se utilizarán en el filtro
*/
class FiltroType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options){
    $builder
        ->add('nombre', null, [
          "label" => "Nombre",
          'required' => False,
        ])
        ->setMethod("GET")
    ;
  }

  public function getName(){
    return 'filtro';
  }
}