<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;

use App\Form\PlanillaEquipoType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CollectionType,
    TextType, 
    TextareaType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaEquipoRelojBiometricoType extends AbstractType
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
                'prototype_name' => '__acceso_prot__',
                'label' => false,
            ])
            ->add('cantidadPersonasCargadas', TextType::class, [
                'label' => 'Cantidad Personal Cargado',
            ])
            ->add('capacidadCarga', TextType::class, [
                'label' => 'Capacidad de Carga',
            ])
            ->add('fuente', TextType::class, [
                'label' => 'Fuente',
            ])
            ->add('ups', TextType::class, [
                'label' => 'UPS/Estabilizador',
            ])
            ->add('red', TextType::class, [
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