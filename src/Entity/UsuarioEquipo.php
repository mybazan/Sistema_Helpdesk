<?php

namespace App\Entity;

use App\Repository\UsuarioEquipoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsuarioEquipoRepository::class)
 */
class UsuarioEquipo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personal", inversedBy="equipos", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipo", inversedBy="usuarios", cascade={"persist"})
     * @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     */
    private $equipo;

     /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $fechaInicio; 

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaFin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="modificadoPor_id", referencedColumnName="id")
     */
    private $modificadoPor;

    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    private $isActual;

    public function __construct(){
        $this->isActual = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    public function getIsActual(): ?bool
    {
        return $this->isActual;
    }

    public function setIsActual(?bool $isActual): self
    {
        $this->isActual = $isActual;

        return $this;
    }

    public function getUsuario(): ?Personal
    {
        return $this->usuario;
    }

    public function setUsuario(?Personal $usuario): self
    {
        $this->usuario = $usuario;

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

    public function getModificadoPor(): ?User
    {
        return $this->modificadoPor;
    }

    public function setModificadoPor(?User $modificadoPor): self
    {
        $this->modificadoPor = $modificadoPor;

        return $this;
    }
}
