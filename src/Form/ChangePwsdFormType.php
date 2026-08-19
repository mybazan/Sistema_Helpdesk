<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePwsdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translator = $options['translator'];

        $builder
            ->add("justpassword", PasswordType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'mb-3',
                    'placeholder' => 'Ingrese contraseña actual...',
                ],
                "required" => true,
                "mapped" => false,
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío"]),
                    new UserPassword(["message" => "Contraseña incorecta"])
                ]
            ])
            ->add("newpassword", RepeatedType::class, [
                "mapped" => false,
                'invalid_message' => "Las nuevas contraseñas deben coincidir",
                "type" => PasswordType::class,
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío"])
                ],
                "first_options"  => [
                    'label' => false,
                    'attr' => [
                        'class' => 'mb-3',
                        'placeholder' => 'Ingrese nueva contraseña...',
                    ]

                ],
                "second_options"  => [
                    'label' => false,
                    'attr' => [
                        'class' => 'mb-3',
                        'placeholder' => 'Repita nueva contraseña...',
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');

        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
