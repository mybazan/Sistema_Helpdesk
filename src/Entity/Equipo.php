<?php

namespace App\Entity;

use App\Repository\EquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EquipoRepository::class)
 * @UniqueEntity(fields={"mac"})
 * @UniqueEntity(fields={"nombre"},message="El nombre del equipo ya está en uso.")
 */
class Equipo
{ 
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mac;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;
    
    /**
     * @ORM\ManyToOne(targetEntity=Ubicacion::class, inversedBy="equipos")
     * @ORM\JoinColumn(name="ubicacion_id", referencedColumnName="id")
     */
    private $ubicacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $croquis;
    
    /**
     * @ORM\OneToMany(targetEntity=Caracteristica::class, mappedBy="equipo")
     */
    private $caracteristicas;
    
    /**
     * @ORM\OneToMany(targetEntity=UsuarioEquipo::class, mappedBy="equipo")
     */ 
    private $usuarios;
    
    /**
     * @ORM\OneToMany(targetEntity=EquipoHistorial::class, mappedBy="equipo", cascade={"remove"})
     * 
     */
    private $equipoHistorial;
    
    /**
     * @ORM\OneToOne(targetEntity=PlanillaEquipo::class, inversedBy="equipo", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="planilla_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $planillaEquipo;

    /**
     * @ORM\ManyToOne(targetEntity=TipoEquipo::class, inversedBy="equipos", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_id", referencedColumnName="id")
     */
    private $tipo;
    
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Equipo", inversedBy="children")
     * @ORM\JoinColumn(name="parent_parentid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Equipo", mappedBy="parent")
     */
    private $children;
    
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Regex(
	 *     pattern="/^[0-9]+$/",
	 *     match=true,
	 *     message="Este campo solo acepta números positivos."
	 * )
	 */
    private $condicion;

    public function __construct()
    {
        $this->equipoHistorial = new ArrayCollection();
        $this->caracteristicas = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(?string $mac): self
    {
        $this->mac = $mac;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

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

    public function getCroquis(): ?string
    {
        return $this->croquis;
    }

    public function setCroquis(?string $croquis): self
    {
        $this->croquis = $croquis;

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
            $caracteristica->setEquipo($this);
        }

        return $this;
    }

    public function removeCaracteristica(Caracteristica $caracteristica): self
    {
        if ($this->caracteristicas->removeElement($caracteristica)) {
            // set the owning side to null (unless already changed)
            if ($caracteristica->getEquipo() === $this) {
                $caracteristica->setEquipo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioEquipo>
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(UsuarioEquipo $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setEquipo($this);
        }

        return $this;
    }

    public function removeUsuario(UsuarioEquipo $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getEquipo() === $this) {
                $usuario->setEquipo(null);
            }
        }

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

    public function getTipo(): ?TipoEquipo
    {
        return $this->tipo;
    }

    public function setTipo(?TipoEquipo $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Equipo>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Equipo $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Equipo $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, EquipoHistorial>
     */
    public function getEquipoHistorial(): Collection
    {
        return $this->equipoHistorial;
    }

    public function addEquipoHistorial(EquipoHistorial $equipoHistorial): self
    {
        if (!$this->equipoHistorial->contains($equipoHistorial)) {
            $this->equipoHistorial[] = $equipoHistorial;
            $equipoHistorial->setEquipo($this);
        }

        return $this;
    }

    public function removeEquipoHistorial(EquipoHistorial $equipoHistorial): self
    {
        if ($this->equipoHistorial->removeElement($equipoHistorial)) {
            // set the owning side to null (unless already changed)
            if ($equipoHistorial->getEquipo() === $this) {
                $equipoHistorial->setEquipo(null);
            }
        }

        return $this;
    }

    public function getUbicacion(): ?Ubicacion
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?Ubicacion $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    public function getCondicion(): ?string
    {
        return $this->condicion;
    }

    public function setCondicion(?string $condicion): self
    {
        $this->condicion = $condicion;

        return $this;
    }


}
