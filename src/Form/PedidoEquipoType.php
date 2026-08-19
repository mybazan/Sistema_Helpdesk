<?php

namespace App\Form;

use App\Entity\Equipo;
use App\Entity\PedidoEquipo;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;
class PedidoEquipoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $formEvent) {
            $equipo = $formEvent->getData();
            $form = $formEvent->getForm();

            $form
                ->add("equipo", EntityType::class, [
                    "class" => Equipo::class,
                    "required" => true,
                    "placeholder" => "Seleccione un equipo.",
                    "label" => "Cámara *",
                    "query_builder" => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->join('e.tipo', 't')
                            ->andWhere('t.nombre LIKE :tipo_uno')
                            ->setParameter('tipo_uno', '%DVR%')
                            ->orWhere('t.nombre LIKE :tipo_dos')
                            ->setParameter('tipo_dos', '%Cámara%')
                            ->orderBy('e.nombre', 'ASC');
                    },
                    "constraints" => [
                        new NotBlank(["message" => "El campo no puede estar vacío"])
                    ],
                    'choice_label' => function ($equipo) {
                        return sprintf($equipo->getNombre());
                    },
                    'empty_data' => ''
                ])
                ->add("fechaInicio", DateTimeType::class, [
                    'label' => "Fecha de Inicio *",
                    'placeholder' => [
                        'year' => 'Año',
                        'month' => 'Mes',
                        'day' => 'Día',
                        'hour' => 'Hora',
                        'minute' => 'Minuto'
                    ],
                    'years' => range(date('Y') - 1, date('Y')),
                    'input_format' => 'd-m-Y H:i'
                ])
                ->add("fechaFin", DateTimeType::class, 
                [
                    'label' => "Fecha de Fin *",
                    'placeholder' => [
                        'year' => 'Año',
                        'month' => 'Mes',
                        'day' => 'Día',
                        'hour' => 'Hora',
                        'minute' => 'Minuto'
                    ],
                    'years' => range(date('Y') - 1, date('Y')),
                    'input_format' => 'd-m-Y H:i'
                ]);

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PedidoEquipo::class,
            'csrf_protection' => false,
        ]);
    }
}
