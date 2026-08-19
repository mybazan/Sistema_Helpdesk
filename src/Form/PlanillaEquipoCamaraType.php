<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;
use App\Form\PlanillaEquipoType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaEquipoCamaraType extends AbstractType
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
            ->add('almacenamientos', CollectionType::class, [
                'entry_type' => PlanillaEquipoAlmacenamientoType::class,
                'prototype_name' => '__almacenamientos__',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'label' => false,
            ])
            ->add('megapixeles', TextType::class, [
                'label' => 'Megapixeles',
                'attr' => [
                    'placeholder' => 'Ej: 2mp',
                ],
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