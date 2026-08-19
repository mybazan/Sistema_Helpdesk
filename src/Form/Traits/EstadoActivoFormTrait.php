<?php

namespace App\Form\Traits;

use App\Form\Type\EstadoActivoType;
use Symfony\Component\Form\FormBuilderInterface;

trait EstadoActivoFormTrait
{
    private function addEstadoActivoField(FormBuilderInterface $builder, bool $mostrarEstado): void
    {
        if ($mostrarEstado) {
            $builder->add('isActive', EstadoActivoType::class);
        }
    }
}
