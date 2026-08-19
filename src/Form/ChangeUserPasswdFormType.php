<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeUserPasswdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add("newpassword", RepeatedType::class, [
                "mapped" => false,
                'invalid_message' => "Las contraseñas deben ser idénticas",
                "type" => PasswordType::class,
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío"])
                ],
                "first_options"  => [
                    'label' => false,
                    'attr' => [
                        'class' => 'mb-2',
                        'placeholder' => 'Ingrese la nueva contraseña...',
                    ]

                ],
                "second_options"  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Repita la nueva contraseña...',
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //
    }
}
