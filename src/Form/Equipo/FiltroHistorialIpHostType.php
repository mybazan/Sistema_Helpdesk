<?php

namespace App\Form\Equipo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltroHistorialIpHostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("host", TextType::class, [
                'label' => "Nombre del Equipo",
                'required' => false, 
                'attr' => [
                'placeholder' => 'Host',
                ],
            ])
            ->add("ip", TextType::class, [
                'label' => "IP del Equipo",
                'required' => false, 
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
