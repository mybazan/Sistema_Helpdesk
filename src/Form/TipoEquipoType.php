<?php

namespace App\Form;

use App\Entity\TipoEquipo;
use App\Form\Traits\EstadoActivoFormTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class TipoEquipoType extends AbstractType
{
    use EstadoActivoFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nombre',
                TextType::class,
                [
                    'label' => "Nombre del Equipo *",
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "El campo no puede estar vacío"])
                    ],
                    'empty_data' => '',
                ]
            )
            ->add(
                'nomenclatura',
                TextType::class,
                [
                    'label' => "Nomenclatura *",
                    'required' => true,
                    'attr' => [
                        'maxlength' => 2,
                    
                    ],
                    'constraints' => [
                        new NotBlank(["message" => "El campo no puede estar vacío"]),

                        new Assert\Regex([
                            'pattern' => '/^[A-Za-z]{2}$/',
                            'message' => 'Debe ingresar exactamente 2 letras.'
                        ])
                    ],

                    'empty_data' => '',
                ]
            )
        ;

        $this->addEstadoActivoField($builder, $options['is_edit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TipoEquipo::class,
            'is_edit' => false,
        ]);
    }
}
