<?php

namespace App\Form;

use App\Entity\PedidoEstado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedidoEstadoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre *',
                'required' => true,
                'empty_data' => '',
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Activo' => true,
                    'Inactivo' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PedidoEstado::class,
        ]);
    }
}
