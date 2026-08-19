<?php

namespace App\Form\Personal;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Personal;
use App\Entity\Ubicacion;

/*
* Esta clase define los campos que se utilizarán en el filtro
*/
class FiltroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
          ->add('apellido', null, [
            "label" => "Apellido",
            'required' => False,
          ])
          ->add('nombre', null, [
            "label" => "Nombre",
            'required' => False,
          ])
          ->add('dni', null, [
            "label" => "Dni",
            'required' => False,
          ])
          ->add("ubicacion", EntityType::class, [
            "label" => "Ubicación",
            "class" => Ubicacion::class,
            "required" => False,
            "placeholder" => "Todas",
            'choice_label' => 'nomenclatura',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.isActive = :isActive')
                    ->setParameter('isActive', true)
                    ->orderBy('u.nomenclatura', 'ASC');
            },
        ])
          ->setMethod("GET")
      ;
    }

    public function getName()
    {
        return 'filtro';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
          'csrf_protection' => false,
      ));
    }
}
