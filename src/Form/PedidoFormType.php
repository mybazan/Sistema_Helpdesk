<?php

namespace App\Form;

use App\Entity\Pedido;
use App\Entity\Ubicacion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PedidoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('solicitanteTexto', TextType::class, [
                'label' => 'Solicitante *',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'El campo no puede estar vacío']),
                ],
                'empty_data' => '',
            ])
            ->add('ubicacionPedido', EntityType::class, [
                'class' => Ubicacion::class,
                'label' => 'Ubicación *',
                'required' => true,
                'placeholder' => 'Seleccione una ubicación',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('u.nombre', 'ASC');
                },
                'choice_label' => function (Ubicacion $ubicacion) {
                    return sprintf('%s (%s)', $ubicacion->getNombre(), $ubicacion->getNomenclatura());
                },
                'constraints' => [
                    new NotBlank(['message' => 'Debe seleccionar una ubicación']),
                ],
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('ubicacionTexto', TextType::class, [
                'label' => 'Detalle de ubicación',
                'required' => false,
                'help' => 'Opcional. Si se deja vacío se usa la nomenclatura de la ubicación.',
            ])
            ->add('solicitud', TextType::class, [
                'label' => 'Solicitud *',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'El campo no puede estar vacío']),
                ],
                'empty_data' => '',
            ])
            ->add('observacion', TextType::class, [
                'label' => 'Observación',
                'required' => false,
            ])
            ->add('prioridad', ChoiceType::class, [
                'label' => 'Prioridad *',
                'required' => true,
                'placeholder' => 'Seleccione una prioridad al pedido',
                'choices' => [
                    'Alta' => 1,
                    'Media' => 2,
                    'Baja' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pedido::class,
        ]);
    }
}
