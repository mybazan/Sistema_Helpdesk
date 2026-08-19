<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;

use App\Form\PlanillaEquipoType;
use App\Form\PlanillaEquipoAccesoType;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    CollectionType,
    TextType, 
    TextareaType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaEquipoDvrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accesos', CollectionType::class, [
                'entry_type' => PlanillaEquipoAccesoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'prototype_name' => '__acceso_prot__',
            ])
            ->add('canales', TextType::class, [
                'label' => 'Canales',
            ])
            ->add('canalesLibres', TextType::class, [
                'label' => 'Canales libres',
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
            ->add('resolucionGrabacion', TextType::class, [
                'label' => 'Resolución de Grabación',
                'attr' => [
                    'placeholder' => 'Ej: 1080p',
                ]
            ])
            ->add('tiempoEstimadoGrabacion', TextType::class, [
                'label' => 'Tiempo estimado de Grabación',
                'attr' => [
                    'placeholder' => 'Ej: 10 Días',
                ]
            ])
            ->add('ups', TextType::class, [
                'label' => 'UPS/Estabilizador',
            ])
            ->add('red', TextType::class, [
                'label' => 'Red',
            ])
            ->add('observacion', TextareaType::class, [
                'label' => 'Observación',
            ]);
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