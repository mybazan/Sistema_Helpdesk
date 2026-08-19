<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;
use App\Form\PlanillaEquipoType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaEquipoImpresoraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('toner', TextType::class, [
                'label' => 'Toner',
            ])
            ->add('drum', TextType::class, [
                'label' => 'DRUM',
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