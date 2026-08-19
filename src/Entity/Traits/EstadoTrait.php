<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estado habilitado/deshabilitado para entidades administrativas (Ubicacion, TipoEquipo, etc.).
 * No usar en Pedido ni en PedidoEstado: el ticket maneja su flujo vía PedidoHistorialEstado.
 */
trait EstadoTrait
{
    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $isActive = true;

    public function getIsActive(): bool
    {
        return (bool) $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getEstadoLabel(): string
    {
        return $this->getIsActive() ? 'Habilitado' : 'Deshabilitado';
    }

    public function isHabilitado(): bool
    {
        return $this->getIsActive();
    }

    public function habilitar(): self
    {
        return $this->setIsActive(true);
    }

    public function deshabilitar(): self
    {
        return $this->setIsActive(false);
    }
}
