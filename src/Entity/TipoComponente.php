<?php

namespace App\Entity;

use App\Entity\Traits\EstadoTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categoría de característica de equipo (ej. Procesador, RAM). No es stock.
 *
 * @ORM\Entity
 * @ORM\Table(name="tipo_componente")
 */
class TipoComponente
{
    use EstadoTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificacion;

    /**
     * @ORM\OneToMany(targetEntity=CaracteristicaTipoEquipo::class, mappedBy="tipoComponente")
     */
    private $tipoEquipo;

    public function __construct()
    {
        $this->tipoEquipo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentificacion(): ?string
    {
        return $this->identificacion;
    }

    public function setIdentificacion(string $identificacion): self
    {
        $this->identificacion = $identificacion;

        return $this;
    }

    /**
     * @return Collection<int, CaracteristicaTipoEquipo>
     */
    public function getTipoEquipo(): Collection
    {
        return $this->tipoEquipo;
    }

    public function addTipoEquipo(CaracteristicaTipoEquipo $tipoEquipo): self
    {
        if (!$this->tipoEquipo->contains($tipoEquipo)) {
            $this->tipoEquipo[] = $tipoEquipo;
            $tipoEquipo->setTipoComponente($this);
        }

        return $this;
    }

    public function removeTipoEquipo(CaracteristicaTipoEquipo $tipoEquipo): self
    {
        if ($this->tipoEquipo->removeElement($tipoEquipo)) {
            if ($tipoEquipo->getTipoComponente() === $this) {
                $tipoEquipo->setTipoComponente(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->identificacion ?? '';
    }
}
