<?php

namespace App\Form;

use App\Entity\PlanillaEquipoAlmacenamiento;
use Svg\Tag\Text;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Choice;

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

class PlanillaEquipoAlmacenamientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $formEvent) {
                $planillaEquipo = $formEvent->getData();
                $almacenamiento = $formEvent->getData();
                $form = $formEvent->getForm();
                $form
                    ->add('tipo', ChoiceType::class, [
                        'label' => 'Tipo',
                        'required' => true,
                        'choices' => [
                            'SSD' => 'SSD',
                            'HDD' => 'HDD',
                            'NVMe' => 'NVMe',
                            'M.2' => 'M.2',
                            'SD' => 'SD',
                            'microSD' => 'microSD',
                            'Otro' => 'Otro',
                        ],
                        'placeholder' => 'Seleccionar tipo',
                        'attr' => [
                            'class' => 'form-control',
                        ],

                    ])
                    ->add('capacidad', ChoiceType::class, [
                        'label' => 'Capacidad de almacenamiento',
                        'required' => true,
                        'choices' => [
                            '4 GB' => 4,
                            '8 GB' => 8,
                            '16 GB' => 16,
                            '32 GB' => 32,
                            '64 GB' => 64,
                            '120 GB' => 120,
                            '128 GB' => 128,
                            '240 GB' => 240,
                            '250 GB' => 250,
                            '256 GB' => 256,
                            '480 GB' => 480,
                            '500 GB' => 500,
                            '512 GB' => 512,
                            '960 GB' => 960,
                            '1000 GB (1 TB)' => 1000,
                            '1920 GB (1.92 TB)' => 1920,
                            '2000 GB (2 TB)' => 2000,
                            '2400 GB (2.4 TB)' => 2400,
                            '3840 GB (3.84 TB)' => 3840,
                            '4000 GB (4 TB)' => 4000,
                            '6000 GB (6 TB)' => 6000,
                            '7680 GB (7.68 TB)' => 7680,
                            '8000 GB (8 TB)' => 8000,
                            '10000 GB (10 TB)' => 10000,
                            '12000 GB (12 TB)' => 12000,
                            '14000 GB (14 TB)' => 14000,
                            '15360 GB (15.36 TB)' => 15360,
                            '16000 GB (16 TB)' => 16000,
                            '18000 GB (18 TB)' => 18000,
                            '20000 GB (20 TB)' => 20000,
                            '22000 GB (22 TB)' => 22000,
                            '24000 GB (24 TB)' => 24000,
                            '30720 GB (30.72 TB)' => 30720,
                            '36000 GB (36 TB)' => 36000,
                            '40000 GB (40 TB)' => 40000,
                            '48000 GB (48 TB)' => 48000,
                            '60000 GB (60 TB)' => 60000,
                            '100000 GB (100 TB)' => 100000,
                        ],
                        'placeholder' => 'Seleccionar capacidad',
                        'choice_attr' => function ($choice, $key, $value) {
                            return ['data-capacity' => $value];
                        }
                    ])
                    ->add('rol', ChoiceType::class, [
                        'label' => 'Rol',
                        'required' => true,
                        'choices' => [
                            'Primario' => 1,
                            'Secundario' => 2,
                            'Terciario' => 3,
                            'Backup' => 4,
                            'Sin Uso' => 5,
                        ],
                        'placeholder' => 'Seleccionar rol',
                    ])
                    ->add('observacion', TextType::class, [
                        'label' => 'Observación',
                    ])
                ;
            }

        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanillaEquipoAlmacenamiento::class,
        ]);
    }
}
