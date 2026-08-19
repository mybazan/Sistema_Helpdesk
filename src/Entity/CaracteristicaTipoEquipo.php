<?php

namespace App\Entity;

use App\Entity\Traits\EstadoTrait;
use App\Repository\CaracteristicaTipoEquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaracteristicaTipoEquipoRepository::class)
 */
class CaracteristicaTipoEquipo
{
    use EstadoTrait;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoComponente", inversedBy="tipoEquipo", cascade={"persist"})
     * @ORM\JoinColumn(name="tipoComponente_id", referencedColumnName="id")
     */
    private $tipoComponente; 

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoEquipo", inversedBy="caracteristicas", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_id", referencedColumnName="id")
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity=Caracteristica::class, mappedBy="caracteristica")
     */
    private $caracteristicas;

    public function __construct()
    {
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

    public function getTipo(): ?TipoEquipo
    {
        return $this->tipo;
    }

    public function setTipo(?TipoEquipo $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection<int, Caracteristica>
     */
    public function getCaracteristicas(): Collection
    {
        return $this->caracteristicas;
    }

    public function addCaracteristica(Caracteristica $caracteristica): self
    {
        if (!$this->caracteristicas->contains($caracteristica)) {
            $this->caracteristicas[] = $caracteristica;
            $caracteristica->setCaracteristica($this);
        }

        return $this;
    }

    public function removeCaracteristica(Caracteristica $caracteristica): self
    {
        if ($this->caracteristicas->removeElement($caracteristica)) {
            // set the owning side to null (unless already changed)
            if ($caracteristica->getCaracteristica() === $this) {
                $caracteristica->setCaracteristica(null);
            }
        }

        return $this;
    }

    public function getTipoComponente(): ?TipoComponente
    {
        return $this->tipoComponente;
    }

    public function setTipoComponente(?TipoComponente $tipoComponente): self
    {
        $this->tipoComponente = $tipoComponente;

        return $this;
    }
}
