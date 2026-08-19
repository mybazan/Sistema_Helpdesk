<?php

namespace App\Entity;

use App\Repository\PedidoEstadoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Catálogo de estados del flujo de tickets (Recibido, Asignado, Finalizado, etc.).
 * No confundir con el estado habilitado/deshabilitado de entidades administrativas.
 *
 * @ORM\Entity(repositoryClass=PedidoEstadoRepository::class)
 */
class PedidoEstado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * Indica si el estado está disponible en el sistema.
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @ORM\OneToMany(targetEntity=PedidoHistorialEstado::class, mappedBy="pedidoEstado")
     */
    private $pedidoHistorialEstados;

    public function __construct()
    {
        $this->pedidoHistorialEstados = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, PedidoHistorialEstado>
     */
    public function getPedidoHistorialEstados(): Collection
    {
        return $this->pedidoHistorialEstados;
    }

    public function addPedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if (!$this->pedidoHistorialEstados->contains($pedidoHistorialEstado)) {
            $this->pedidoHistorialEstados[] = $pedidoHistorialEstado;
            $pedidoHistorialEstado->setPedidoEstado($this);
        }

        return $this;
    }

    public function removePedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if ($this->pedidoHistorialEstados->removeElement($pedidoHistorialEstado)) {
            if ($pedidoHistorialEstado->getPedidoEstado() === $this) {
                $pedidoHistorialEstado->setPedidoEstado(null);
            }
        }

        return $this;
    }
}
