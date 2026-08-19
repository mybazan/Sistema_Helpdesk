<?php

namespace App\Form;

use App\Entity\Caracteristica;
use App\Entity\CaracteristicaTipoEquipo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CaracteristicaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder               
            ->add("caracteristica", EntityType::class, [
                //"mapped" => false,
                "label" => "Caracteristica *",
                "class" => CaracteristicaTipoEquipo::class,
                "required" => true,
                "placeholder" => "Seleccione el Caracteristica.",
                "constraints" => [
                    new NotBlank(["message" => "El campo no puede estar vacío."])
                ],
                'choice_label' => 'nombre'
            ])
            ->add("descripcion", TextType::class, 
            [
                'label' => "Descripción *",
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Caracteristica::class,
        ]);
    }
}
