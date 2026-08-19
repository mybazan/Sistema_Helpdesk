<?php

namespace App\Form\pedido;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Pedido;
use App\Entity\User;
use App\Entity\PedidoEstado;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;

/*
* Esta clase define los campos que se utilizarán en el filtro
*/
class FiltroType extends AbstractType
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('id', null, [
        "label" => "Nº de Ticket",
        'required' => False,
      ])
      ->add('fecha', DateType::class, [
        'widget' => 'choice',
        'html5' => false,
        'placeholder' => [
          'day' => 'Día',
          'month' => 'Mes',
          'year' => 'Año',
        ], 
        'format' => 'dd/MM/yyyy',
        'required' => false,
        'years' => range(2020, (date('Y') + 1)),
        "label" => "Fecha",
        'by_reference' => false,
      ])
      ->add('solicitanteTexto', TextType::class, [
        'label' => 'Solicitante',
        'required' => false,
      ])
      ->add('ubicacionTexto', TextType::class, [
        'label' => 'Ubicación',
        'required' => false,
      ])
      ->add('solicitud', null, [
        "label" => "Solicitud",
        'required' => False,
      ])
    ;
    
    if($this->security->isGranted('TICKET_ASIGNAR')){
      $builder
        ->add("personal", EntityType::class, [
          "class" => User::class,
          "required" => False,
          "placeholder" => "Seleccione un técnico encargado.",
          "label" => "Encargado",
          'query_builder' => function (EntityRepository  $er) {
              return $er->createQueryBuilder('u')
                  ->andWhere('u.isTecnico <> 0')
                  ->orderBy('u.apellido', 'ASC');
          },
          'choice_label' => function ($usuario) {
              return sprintf('%s, %s', $usuario->getApellido(), $usuario->getNombre());
          },
          'attr' => [
              'class' => 'select2',
          ],
        ])
      ;
    }

    $builder
      ->add("prioridad", ChoiceType::class, [
        'label' => "Prioridad",
        'required' => False,
        "placeholder" => "Seleccione una prioridad del pedido",
        'choices'  => [
            'Alta' => 1,
            'Media' => 2,
            'Baja' => 3,
        ]
      ])
      ->add("estado", EntityType::class, [
        "class" => PedidoEstado::class,
        "required" => False,
        "placeholder" => "Seleccione un estado.",
        "label" => "Estado",
        'query_builder' => function (EntityRepository  $er) {
            return $er->createQueryBuilder('p');
        },
        'choice_label' => function ($estado) {
            return sprintf('%s', $estado->getNombre());
        },
        'attr' => [
            'class' => 'select2'
        ],
      ])
      ->setMethod("GET")
    ;
  }

  public function getName()
  {
      return 'filtro';
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
        'csrf_protection' => false,
    ));
  }
}
