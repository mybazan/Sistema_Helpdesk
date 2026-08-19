<?php

namespace App\Form;

use App\Entity\CaracteristicaTipoEquipo;
use App\Form\Traits\EstadoActivoFormTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CaracteristicaTipoEquipoType extends AbstractType
{
    use EstadoActivoFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("nombre", TextType::class, 
            [
                'label' => "Nombre",
                'required' => false,
            ])
        ;

        $this->addEstadoActivoField($builder, $options['is_edit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CaracteristicaTipoEquipo::class,
            'is_edit' => false,
        ]);
    }
}
