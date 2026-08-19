<?php

namespace App\Form;

use App\Entity\Ubicacion;
use App\Form\Traits\EstadoActivoFormTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UbicacionType extends AbstractType
{
    use EstadoActivoFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre *',
                'constraints' => [
                    new NotBlank(['message' => 'El campo no puede estar vacío']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: Oficina principal',
                ],
            ])
            ->add('nomenclatura', TextType::class, [
                'label' => 'Nomenclatura *',
                'constraints' => [
                    new NotBlank(['message' => 'El campo no puede estar vacío']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: OF-01',
                ],
            ])
        ;

        $this->addEstadoActivoField($builder, $options['is_edit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ubicacion::class,
            'is_edit' => false,
        ]);
    }
}
