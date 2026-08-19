<?php

namespace App\Form;

use App\Entity\Equipo;
use App\Entity\TipoEquipo;
use App\Entity\Ubicacion;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EquipoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('condicion', ChoiceType::class, [
                'label' => 'Condición',
                'required' => false,
                'placeholder' => 'Seleccione una opción',
                'choices' => [
                    'Activo'            => 1,
                    'Prestado'          => 2,
                    'Fuera de Servicio' => 3,
                ],
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add("tipo", EntityType::class, [
                "label" => "Tipo *",
                "class" => TipoEquipo::class,
                "required" => true,
                "placeholder" => "Seleccione el tipo de equipo.",
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
                'query_builder' => function (EntityRepository $te) {
                    return $te->createQueryBuilder('te')
                        ->where('te.isActive = :isActive')
                        ->setParameter('isActive', true)
                        ->orderBy('te.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'choice_attr' => function (TipoEquipo $tipoEquipo) {
                    return ['data-tipoequiponomenclatura' => $tipoEquipo->getNomenclatura()];
                },
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add("ubicacion", EntityType::class, [
                "label" => "Ubicación *",
                "required" => true,
                "class" => Ubicacion::class,
                "placeholder" => "Seleccione una ubicación.",
                'choice_label' => 'nombre',
                'choice_attr' => function (Ubicacion $ubicacion) {
                    return ['data-ubicacionnomenclatura' => $ubicacion->getNomenclatura()];
                },
                'constraints' => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
                'attr' => [
                    'class' => 'select2',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.isActive = :isActive')
                        ->setParameter('isActive', true)
                        ->orderBy('u.nomenclatura', 'ASC');
                },
            ])
            ->add("nombre", TextType::class, [
                'label' => "Nombre del Equipo *",
                'required' => true,
                'attr' => [
                    'readonly' => false,
                    'placeholder' => 'Nombre del equipo',
                ],
            ])
            ->add("mac", TextType::class, [
                'label' => "Dirección MAC",
                'required' => false,
            ])
            ->add("ip", TextType::class, [
                'label' => "Dirección IP",
                'required' => false,
            ])
            ->add("observacion", TextType::class, [
                'label' => "Observación",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipo::class,
        ]);
    }
}