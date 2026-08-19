<?php

namespace App\Form\Equipo;

use App\Entity\Ubicacion;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltroHistorialUbicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("ubicacion", EntityType::class, [
                "label" => "Ubicación",
                "class" => Ubicacion::class,
                "required" => false,
                "placeholder" => "Todas",
                'choice_label' => 'nomenclatura',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.isActive = :isActive')
                        ->setParameter('isActive', true)
                        ->orderBy('u.nomenclatura', 'ASC');
                },
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add("fechaInicio", DateType::class, [
                "required" => false,
                'label' => "Desde",
                'placeholder' => [
                    'year' => 'Año',
                    'month' => 'Mes',
                    'day' => 'Día'
                ],
                'input_format' => 'd-m-Y'
            ])
            ->add("fechaFin", DateType::class, [
                "required" => false,
                'label' => "Hasta",
                'placeholder' => [
                    'year' => 'Año',
                    'month' => 'Mes',
                    'day' => 'Día'
                ],
                'input_format' => 'd-m-Y'
            ])
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
