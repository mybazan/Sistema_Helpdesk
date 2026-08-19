<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ubicacion;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use doctrine\ORM\EntityRepository;
class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translator = $options['translator'];

        $builder
            ->add("username", TextType::class, [
                "label" => "Usuario *"
            ])
            ->add("email", EmailType::class,[
                "label" => "Correo Electrónico *",
                "required" => true,
            ])
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
            ->add("cuil", TextType::class, [
                "label" => "CUIL",
                "required" => false,
            ])
            ->add("telefono", TextType::class, [
                "label" => "Teléfono",
                "required" => false,
            ])
            ->add("direccion", TextType::class, [
                "label" => "Dirección",
                "required" => false,
            ])
            ->add("role", EntityType::class, [
                "label" => "Rol *",
                "mapped" => false,
                "class" => Role::class,
                "required" => true,
                "placeholder" => "Seleccione un rol.",
                'query_builder' => function (RoleRepository $roleRepository) {
                    return $roleRepository->findForActionIndex();
                },
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ]
            ])
            ->add("ubicacion", EntityType::class, [
                "mapped" => false,
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
            ])
            ->add("isTecnico", CheckboxType::class, [
                "label" => "Técnico *",
                "required" => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
