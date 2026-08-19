<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;
use App\Form\PlanillaEquipoType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CollectionType,
    ChoiceType,
    TextareaType,
    TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaEquipoServidorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sistemasOperativos', CollectionType::class, [
                'entry_type' => PlanillaEquipoSistemaOperativoType::class,
                'prototype_name' => '__so_prot__',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ])


            ->add('almacenamientos', CollectionType::class, [
                'entry_type' => PlanillaEquipoAlmacenamientoType::class,
                'prototype_name' => '__almacenamientos__',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'label' => false,
            ])
            ->add('procesador', TextType::class,  [
                'label' => 'Procesador',
                'attr' => [
                    'placeholder' => 'Ej: Ryzen 3 3200G',
                ]
            ])
            ->add('memoriaRAM', ChoiceType::class, [
                'label' => 'Memoria RAM (GB)',
                'placeholder' => 'Seleccione la cantidad de memoria RAM',
                'required' => true,
                'choices' => [
                    '1 GB' => 1,
                    '2 GB' => 2,
                    '3 GB' => 3,
                    '4 GB' => 4,
                    '6 GB' => 6,
                    '8 GB' => 8,
                    '10 GB' => 10,
                    '12 GB' => 12,
                    '16 GB' => 16,
                    '20 GB' => 20,
                    '32 GB' => 32,
                    '64 GB' => 64,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('monitor', TextType::class,  [
                'label' => 'Monitor',
            ])
            ->add('puertos', TextType::class,  [
                'label' => 'Puertos',
            ])
            ->add('ups', TextType::class, [
                'label' => 'UPS/Estabilizador',
            ])
            ->add('red', TextType::class,  [
                'label' => 'Red',
            ])
            ->add('observacion', TextareaType::class, [
                'label' => 'Observación',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanillaEquipo::class,
            'required' => false,
        ]);
    }
    public function getParent(): ?string
    {
        return PlanillaEquipoType::class;
    }
}