<?php

namespace App\Form;

use App\Entity\PlanillaEquipoSistemaOperativo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    ChoiceType,
    TextType,
    CollectionType
};
use Symfony\Component\Form\{
    FormBuilderInterface,
    AbstractType,
    FormEvent,
    FormEvents
};
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanillaEquipoSistemaOperativoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', ChoiceType::class, [
                'label' => 'Sistema Operativo',
                'choices' => [
                    'Windows' => [
                        'Windows 7' => 'Windows 7',
                        'Windows 10' => 'Windows 10',
                        'Windows 11' => 'Windows 11',
                    ],
                    'Linux' => [
                        'Ubuntu' => 'Ubuntu',
                        'Debian' => 'Debian',
                        'Fedora' => 'Fedora',
                    ],
                    'MAC' => [
                        'macOS Monterey' => 'macOS Monterey',
                        'macOS Big Sur' => 'macOS Big Sur',
                        'macOS Ventura' => 'macOS Ventura',
                    ],
                ],
            ])
            ->add('version', TextType::class,  [
                'label' => 'Versión',
                'attr' => [
                    'required' => 'required',
                ],
                'constraints' => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
            ])
            ->add('observacion', TextType::class,  [
                'label' => 'Observación',
            ])
            ->add('accesos', CollectionType::class, [
                'entry_type' => PlanillaEquipoAccesoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__acceso_prot__',
                'label' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanillaEquipoSistemaOperativo::class,
        ]);
    }
}
