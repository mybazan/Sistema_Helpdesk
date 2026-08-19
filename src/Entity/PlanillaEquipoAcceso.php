<?php

namespace App\Entity;

use App\Repository\PlanillaEquipoAccesoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanillaEquipoAccesoRepository::class)
 */
class PlanillaEquipoAcceso
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
    private $aplicacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clave;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @ORM\ManyToOne(targetEntity=PlanillaEquipoSistemaOperativo::class, inversedBy="accesos")
     * @ORM\JoinColumn(name="sistema_operativo_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $sistemaOperativo;

    /**
     * @ORM\ManyToOne(targetEntity=PlanillaEquipo::class, inversedBy="accesos")
     * @ORM\JoinColumn(name="planilla_equipo_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $planillaEquipo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAplicacion(): ?string
    {
        return $this->aplicacion;
    }

    public function setAplicacion(?string $aplicacion): self
    {
        $this->aplicacion = $aplicacion;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(?string $clave): self
    {
        $this->clave = $clave;

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

    public function getSistemaOperativo(): ?PlanillaEquipoSistemaOperativo
    {
        return $this->sistemaOperativo;
    }

    public function setSistemaOperativo(?PlanillaEquipoSistemaOperativo $sistemaOperativo): self
    {
        $this->sistemaOperativo = $sistemaOperativo;

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


  
}