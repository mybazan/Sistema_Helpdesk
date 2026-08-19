<?php

namespace App\Entity;

use App\Repository\PlanillaEquipoSistemaOperativoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanillaEquipoSistemaOperativoRepository::class)
 */
class PlanillaEquipoSistemaOperativo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @ORM\ManyToOne(targetEntity=PlanillaEquipo::class, inversedBy="sistemasOperativos")
     * @ORM\JoinColumn(name="planilla_equipo_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $planillaEquipo;

    /**
     * @ORM\OneToMany(targetEntity=PlanillaEquipoAcceso::class, mappedBy="sistemaOperativo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $accesos;


    public function __construct()
    {
        $this->accesos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

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
    
    public function getPlanillaEquipo(): ?PlanillaEquipo
    {
        return $this->planillaEquipo;
    }

    public function setPlanillaEquipo(?PlanillaEquipo $planillaEquipo): self
    {
        $this->planillaEquipo = $planillaEquipo;

        return $this;
    }

    /**
     * @return Collection<int, PlanillaEquipoAcceso>
     */
    public function getAccesos(): Collection
    {
        return $this->accesos;
    }

    public function addAcceso(PlanillaEquipoAcceso $acceso): self
    {
        if (!$this->accesos->contains($acceso)) {
            $this->accesos[] = $acceso;
            $acceso->setSistemaOperativo($this);
        }

        return $this;
    }

    public function removeAcceso(PlanillaEquipoAcceso $acceso): self
    {
        if ($this->accesos->removeElement($acceso)) {
            // set the owning side to null (unless already changed)
            if ($acceso->getSistemaOperativo() === $this) {
                $acceso->setSistemaOperativo(null);
            }
        }

        return $this;
    }
}