<?php

namespace App\Form;

use App\Entity\Personal;
use App\Entity\Ubicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

use doctrine\ORM\EntityRepository;
class PersonalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("nombre", TextType::class, [
                "label" => "Nombre *",
                "required" => true,
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío"])
                ]
            ])
            ->add("apellido", TextType::class, [
                "label" => "Apellido *",
                "required" => true,
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío"])
                ]
            ])
            ->add("dni", TextType::class,  [
                "label" => "DNI",
                "required" => false,
            ])
            ->add("cuil", TextType::class,  [
                "label" => "CUIL",
                "required" => false,
            ])
            ->add("email", EmailType::class,[
                "label" => "Correo Electrónico *",
            ])
            ->add("telefono", TextType::class, [
                "label" => "Teléfono",
                "required" => false,
            ])
            ->add("ubicacion", EntityType::class, [
                "label" => "Ubicación *",
                "class" => Ubicacion::class,
                "required" => true,
                "placeholder" => "Seleccione una ubicación.",
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
                'choice_label' => 'nomenclatura',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.isActive = :isActive')
                        ->setParameter('isActive', true)
                        ->orderBy('u.nomenclatura', 'ASC');
                },
                'attr' => [
                    'class' => 'select2 select2-modal'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personal::class,
        ]);
    }
}
