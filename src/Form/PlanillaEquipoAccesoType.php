<?php

namespace App\Form;

use App\Entity\PlanillaEquipoAcceso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanillaEquipoAccesoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aplicacion', ChoiceType::class, [
                'label' => 'Aplicación',
                'required' => true,
                'choices' => [
                    'Gestor de Sesión' => [
                        'Usuario Local' => 'Usuario Local',
                    ],
                    'Control y Soporte Remoto' => [
                        'AnyDesk' => 'AnyDesk',
                        'TeamViewer' => 'TeamViewer',
                    ],
                    'Videovigilancia y Seguridad' => [
                        'iVMS' => 'iVMS',
                        'VivoTek' => 'VivoTek',
                    ],
                    'Control de Asistencia' => [
                        'ZKTimeNet' => 'ZKTimeNet',
                    ],
                    'Otros' => [
                        'Otros' => 'Otros',
                    ],
                ],
            ])
            ->add('usuario', TextType::class, [
                'label' => 'ID/Usuario',
                'attr' => [
                    'required' => 'required',
                ],
                'constraints' => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
            ])
            ->add('clave', TextType::class, [
                'label' => 'Contraseña',
                'attr' => [
                    'required' => 'required',
                ],
                'constraints' => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
            ])
            ->add('observacion', TextType::class, [
                'label' => 'Observación',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanillaEquipoAcceso::class,
        ]);
    }
}
