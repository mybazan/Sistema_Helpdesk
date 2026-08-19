<?php

namespace App\Form;

use App\Entity\PlanillaEquipo;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class PlanillaEquipoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marca', TextType::class, [
                'label' => 'Marca',
            ])
            ->add('modelo', TextType::class, [
                'label' => 'Modelo',
            ])
            ->add('nroSerie', TextType::class, [
                'label' => 'Número de serie',
            ])
            ->add('nroInventario', TextType::class, [
                'label' => 'Número de inventario',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}