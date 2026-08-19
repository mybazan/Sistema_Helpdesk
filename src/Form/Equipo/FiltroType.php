<?php

namespace App\Form\Equipo;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\{
  TipoEquipo,
  Ubicacion,
  Personal
};

/*
 * Esta clase define los campos que se utilizarán en el filtro
 */
class FiltroType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('marca', null, [
        "label" => "Marca",
      ])
      ->add('modelo', null, [
        "label" => "Modelo",
      ])
      ->add('nroSerie', null, [
        "label" => "Nº de Serie",
      ])
      ->add('nroInventario', null, [
        "label" => "Nº de Inventario",
      ])
      ->add('condicion', ChoiceType::class, [
        'label' => 'Condición',
        'placeholder' => 'Todos',
        'choices' => [
          'Activo' => 1,
          'Prestado' => 2,
          'Fuera de Servicio' => 3,
          'Sin Condición' => 4,
        ],
      ])
      ->add("tipo", EntityType::class, [
        "label" => "Tipo de Equipo",
        "class" => TipoEquipo::class,
        "placeholder" => "Todos",
        'choice_label' => 'nombre',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('t')
            ->orderBy('t.nombre', 'ASC');
        }
      ])
      ->add("ubicacion", EntityType::class, [
        "label" => "Ubicación",
        "class" => Ubicacion::class,
        "placeholder" => "Todas",
        'choice_label' => 'nomenclatura',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('u')
            ->where('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('u.nomenclatura', 'ASC');
        },
        'attr' => [
          'class' => 'select2',
        ],
      ])
      ->add('nombre', null, [
        "label" => "Nombre del Equipo",
      ])
      ->add('id', null, [
        "label" => "ID",
      ])
      ->add('mac', null, [
        "label" => "Dirección MAC",
      ])
      ->add('ip', null, [
        "label" => "Dirección IP",
      ])
      ->add("usuario", EntityType::class, [
        "label" => "Personal Asignado",
        "class" => Personal::class,
        "placeholder" => "Seleccione un personal.",
        "choice_label" => function (?Personal $p) {
            return $p->getApellido() . ', ' . $p->getNombre();
        },
        "query_builder" => function (EntityRepository $er) {
          return $er->createQueryBuilder('p')
              ->orderBy('p.apellido', 'ASC')
              ->addOrderBy('p.nombre', 'ASC');
        },
        "attr" => [
            'class' => 'select2'
        ],
        "help" => "Aplica a usuarios activos de un equipo."
      ])
      ->add('procesador', null, [
        'label' => 'Procesador',
      ])
      ->add('memoriaRAM', ChoiceType::class, [
        'label' => 'Memoria RAM',
        'choices' => [
          '1 GB' => 1,
          '2 GB' => 2,
          '4 GB' => 4,
          '6 GB' => 6,
          '8 GB' => 8,
          '10 GB' => 10,
          '12 GB' => 12,
          '16 GB' => 16,
          '20 GB' => 20,
          '32 GB' => 32,
          '64 GB' => 64,
        ],
        'placeholder' => 'Todos',
        'attr' => [
          'class' => 'select2',
        ],
      ])
      ->add('sistemaOperativo', ChoiceType::class, [
        'label' => 'Sistema Operativo',
        'choices' => [
          'Windows' => [
            'Windows 7'   => 'Windows 7',
            'Windows 10'  => 'Windows 10',
            'Windows 11'  => 'Windows 11',
          ],
          'Linux' => [
            'Ubuntu' => 'Ubuntu',
            'Debian' => 'Debian',
            'Fedora' => 'Fedora',
          ],
          'MAC' => [
            'macOS Monterey' => 'macOS Monterey',
            'macOS Big Sur'  => 'macOS Big Sur',
            'macOS Ventura'  => 'macOS Ventura',
          ],
        ],
        'placeholder' => 'Todos',
      ])
      ->add('tipoAlmacenamiento', ChoiceType::class, [
        'label' => 'Tipo de Almacenamiento',
        'choices' => [
          'SSD' => 'SSD',
          'HDD' => 'HDD',
          'NVMe' => 'NVMe',
          'M.2' => 'M.2',
          'SD' => 'SD',
          'microSD' => 'microSD',
          'Otro' => 'Otro',
        ],
        'placeholder' => 'Todos',
        'attr' => [
          'class' => 'form-control',
        ],

      ])
      ->add('capacidadAlmacenamiento', ChoiceType::class, [
        'label' => 'Capacidad de almacenamiento',
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
        'placeholder' => 'Todos',
        'choice_attr' => function ($choice, $key, $value) {
          return ['data-capacity' => $value];
        },
        "attr" => [
            'class' => 'select2'
        ],
      ])
      ->add('rolAlmacenamiento', ChoiceType::class, [
        'label' => 'Rol de Almacenamiento',
        'choices' => [
          'Primario' => 1,
          'Secundario' => 2,
          'Terciario' => 3,
          'Backup' => 4,
          'Sin Uso' => 5,
        ],
        'placeholder' => 'Todos',
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
    $resolver->setDefaults([
      'csrf_protection' => false,
      'required' => false,
      'validation_groups' => false
    ]);
  }
}
