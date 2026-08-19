<?php

namespace App\Entity;

use App\Entity\Traits\EstadoTrait;
use App\Repository\TipoEquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TipoEquipoRepository::class)
 * @UniqueEntity(
 *      fields={"nombre"},
 *      errorPath="nombre",
 *      message="El tipo de equipo ingresado ya se encuentra registrado."
 * )
 */
class TipoEquipo
{
    use EstadoTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $nombre;  

    /**
     * @ORM\OneToMany(targetEntity=Equipo::class, mappedBy="tipo")
     */
    private $equipos;

    /**
     * @ORM\OneToMany(targetEntity=CaracteristicaTipoEquipo::class, mappedBy="tipo")
     */
    private $caracteristicas;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $nomenclatura;
    
    public function __construct()
    {
        $this->equipos = new ArrayCollection();
        $this->caracteristicas = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipo>
     */
    public function getEquipos(): Collection
    {
        return $this->equipos;
    }

    public function addEquipo(Equipo $equipo): self
    {
        if (!$this->equipos->contains($equipo)) {
            $this->equipos[] = $equipo;
            $equipo->setTipo($this);
        }

        return $this;
    }

    public function removeEquipo(Equipo $equipo): self
    {
        if ($this->equipos->removeElement($equipo)) {
            // set the owning side to null (unless already changed)
            if ($equipo->getTipo() === $this) {
                $equipo->setTipo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CaracteristicaTipoEquipo>
     */
    public function getCaracteristicas(): Collection
    {
        return $this->caracteristicas;
    }

    public function addCaracteristica(CaracteristicaTipoEquipo $caracteristica): self
    {
        if (!$this->caracteristicas->contains($caracteristica)) {
            $this->caracteristicas[] = $caracteristica;
            $caracteristica->setTipo($this);
        }

        return $this;
    }

    public function removeCaracteristica(CaracteristicaTipoEquipo $caracteristica): self
    {
        if ($this->caracteristicas->removeElement($caracteristica)) {
            // set the owning side to null (unless already changed)
            if ($caracteristica->getTipo() === $this) {
                $caracteristica->setTipo(null);
            }
        }

        return $this;
    }

    public function getNomenclatura(): ?string
    {
        return $this->nomenclatura;
    }

    public function setNomenclatura(?string $nomenclatura): self
    {
        $this->nomenclatura = $nomenclatura;

        return $this;
    }
}
