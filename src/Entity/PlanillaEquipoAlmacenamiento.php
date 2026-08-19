<?php

namespace App\Entity;

use App\Repository\PlanillaEquipoAlmacenamientoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanillaEquipoAlmacenamientoRepository::class)
 */
class PlanillaEquipoAlmacenamiento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PlanillaEquipo::class, inversedBy="almacenamientos")
     * @ORM\JoinColumn(name="planilla_equipo_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $planillaEquipo;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $tipo;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capacidad;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rol;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getCapacidad(): ?int
    {
        return $this->capacidad;
    }

    public function setCapacidad(?int $capacidad): self
    {
        $this->capacidad = $capacidad;

        return $this;
    }
    public function getRol(): ?int
    {
        return $this->rol;
    }

    public function setRol(?int $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    public function getPlanillaEquipo(): ?PlanillaEquipo
    {
        return $this->planillaEquipo;
    }

    public function setPlanillaEquipo(?PlanillaEquipo $planillaEquipo): self
    {
        $this->planillaEquipo = $planillaEquipo;

        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

}
