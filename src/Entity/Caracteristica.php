<?php

namespace App\Entity;

use App\Repository\CaracteristicaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaracteristicaRepository::class)
 */
class Caracteristica
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CaracteristicaTipoEquipo", inversedBy="caracteristicas", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_caracteristica_id", referencedColumnName="id")
     */
    private $caracteristica; 

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipo", inversedBy="caracteristicas", cascade={"persist"})
     * @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     */
    private $equipo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $componente;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): self
    {
        $this->equipo = $equipo;

        return $this;
    }

    public function getCaracteristica(): ?CaracteristicaTipoEquipo
    {
        return $this->caracteristica;
    }

    public function setCaracteristica(?CaracteristicaTipoEquipo $caracteristica): self
    {
        $this->caracteristica = $caracteristica;

        return $this;
    }

    public function getComponente(): ?string
    {
        return $this->componente;
    }

    public function setComponente(string $componente): self
    {
        $this->componente = $componente;

        return $this;
    }
}
