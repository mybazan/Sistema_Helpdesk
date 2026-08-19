<?php

namespace App\Entity;

use App\Repository\PlanillaEquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PlanillaEquipoRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class PlanillaEquipo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Equipo::class, mappedBy="planillaEquipo")
     */
    private $equipo;

    /**
     * @ORM\OneToMany(targetEntity=PlanillaEquipoSistemaOperativo::class, mappedBy="planillaEquipo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $sistemasOperativos;

    /**
     * @ORM\OneToMany(targetEntity=PlanillaEquipoAcceso::class, mappedBy="planillaEquipo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $accesos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marca;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $modelo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nroSerie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nroInventario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cantidadPersonasCargadas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $capacidadCarga;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $velocidad;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $canales;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $canalesLibres;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $procesador;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fuente;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $monitor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $red;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ups;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $puertos;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toner;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $drum;

    /**
     * @ORM\OneToMany(targetEntity=PlanillaEquipoAlmacenamiento::class, mappedBy="planillaEquipo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $almacenamientos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memoriaRAM;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resolucionGrabacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $megapixeles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tiempoEstimadoGrabacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->almacenamientos = new ArrayCollection();
        $this->sistemasOperativos = new ArrayCollection();
        $this->accesos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(?string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getNroSerie(): ?string
    {
        return $this->nroSerie;
    }

    public function setNroSerie(?string $nroSerie): self
    {
        $this->nroSerie = $nroSerie;

        return $this;
    }

    public function getNroInventario(): ?string
    {
        return $this->nroInventario;
    }

    public function setNroInventario(?string $nroInventario): self
    {
        $this->nroInventario = $nroInventario;

        return $this;
    }

    public function getCapacidadCarga(): ?string
    {
        return $this->capacidadCarga;
    }

    public function setCapacidadCarga(?string $capacidadCarga): self
    {
        $this->capacidadCarga = $capacidadCarga;

        return $this;
    }

    public function getCantidadPersonasCargadas(): ?string
    {
        return $this->cantidadPersonasCargadas;
    }

    public function setCantidadPersonasCargadas(?string $cantidadPersonasCargadas): self
    {
        $this->cantidadPersonasCargadas = $cantidadPersonasCargadas;

        return $this;
    }

    public function getCanales(): ?string
    {
        return $this->canales;
    }

    public function setCanales(?string $canales): self
    {
        $this->canales = $canales;

        return $this;
    }

    public function getCanalesLibres(): ?string
    {
        return $this->canalesLibres;
    }

    public function setCanalesLibres(?string $canalesLibres): self
    {
        $this->canalesLibres = $canalesLibres;

        return $this;
    }

    public function getRed(): ?string
    {
        return $this->red;
    }

    public function setRed(?string $red): self
    {
        $this->red = $red;

        return $this;
    }

    /**
     * @return Collection<int, PlanillaEquipoAlmacenamiento>
     */
    public function getAlmacenamientos(): Collection
    {
        return $this->almacenamientos;
    }

    public function addAlmacenamiento(PlanillaEquipoAlmacenamiento $almacenamiento): self
    {
        if (!$this->almacenamientos->contains($almacenamiento)) {
            $this->almacenamientos[] = $almacenamiento;
            $almacenamiento->setPlanillaEquipo($this);
        }

        return $this;
    }

    public function removeAlmacenamiento(PlanillaEquipoAlmacenamiento $almacenamiento): self
    {
        if ($this->almacenamientos->removeElement($almacenamiento)) {
            // set the owning side to null (unless already changed)
            if ($almacenamiento->getPlanillaEquipo() === $this) {
                $almacenamiento->setPlanillaEquipo(null);
            }
        }

        return $this;
    }

    public function getResolucionGrabacion(): ?string
    {
        return $this->resolucionGrabacion;
    }

    public function setResolucionGrabacion(?string $resolucionGrabacion): self
    {
        $this->resolucionGrabacion = $resolucionGrabacion;

        return $this;
    }

    public function getMegapixeles(): ?string
    {
        return $this->megapixeles;
    }

    public function setMegapixeles(?string $megapixeles): self
    {
        $this->megapixeles = $megapixeles;

        return $this;
    }

    public function getTiempoEstimadoGrabacion(): ?string
    {
        return $this->tiempoEstimadoGrabacion;
    }

    public function setTiempoEstimadoGrabacion(?string $tiempoEstimadoGrabacion): self
    {
        $this->tiempoEstimadoGrabacion = $tiempoEstimadoGrabacion;

        return $this;
    }

    public function getUps(): ?string
    {
        return $this->ups;
    }

    public function setUps(?string $ups): self
    {
        $this->ups = $ups;

        return $this;
    }


    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): self
    {
        // unset the owning side of the relation if necessary
        if ($equipo === null && $this->equipo !== null) {
            $this->equipo->setPlanillaEquipo(null);
        }

        // set the owning side of the relation if necessary
        if ($equipo !== null && $equipo->getPlanillaEquipo() !== $this) {
            $equipo->setPlanillaEquipo($this);
        }

        $this->equipo = $equipo;

        return $this;
    }

    public function getMemoriaRAM(): ?string
    {
        return $this->memoriaRAM;
    }

    public function setMemoriaRAM(?string $memoriaRAM): self
    {
        $this->memoriaRAM = $memoriaRAM;

        return $this;
    }

    public function getProcesador(): ?string
    {
        return $this->procesador;
    }

    public function setProcesador(?string $procesador): self
    {
        $this->procesador = $procesador;

        return $this;
    }

    public function getFuente(): ?string
    {
        return $this->fuente;
    }

    public function setFuente(?string $fuente): self
    {
        $this->fuente = $fuente;

        return $this;
    }

    public function getMonitor(): ?string
    {
        return $this->monitor;
    }

    public function setMonitor(?string $monitor): self
    {
        $this->monitor = $monitor;

        return $this;
    }

    public function getVelocidad(): ?string
    {
        return $this->velocidad;
    }

    public function setVelocidad(?string $velocidad): self
    {
        $this->velocidad = $velocidad;

        return $this;
    }

    public function getPuertos(): ?string
    {
        return $this->puertos;
    }

    public function setPuertos(?string $puertos): self
    {
        $this->puertos = $puertos;

        return $this;
    }

    public function getToner(): ?string
    {
        return $this->toner;
    }

    public function setToner(?string $toner): self
    {
        $this->toner = $toner;

        return $this;
    }

    public function getDrum(): ?string
    {
        return $this->drum;
    }

    public function setDrum(?string $drum): self
    {
        $this->drum = $drum;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection<int, PlanillaEquipoSistemaOperativo>
     */
    public function getSistemasOperativos(): Collection
    {
        return $this->sistemasOperativos;
    }

    public function addSistemasOperativo(PlanillaEquipoSistemaOperativo $sistemasOperativo): self
    {
        if (!$this->sistemasOperativos->contains($sistemasOperativo)) {
            $this->sistemasOperativos[] = $sistemasOperativo;
            $sistemasOperativo->setPlanillaEquipo($this);
        }

        return $this;
    }

    public function removeSistemasOperativo(PlanillaEquipoSistemaOperativo $sistemasOperativo): self
    {
        if ($this->sistemasOperativos->removeElement($sistemasOperativo)) {
            // set the owning side to null (unless already changed)
            if ($sistemasOperativo->getPlanillaEquipo() === $this) {
                $sistemasOperativo->setPlanillaEquipo(null);
            }
        }

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
            $acceso->setPlanillaEquipo($this);
        }

        return $this;
    }

    public function removeAcceso(PlanillaEquipoAcceso $acceso): self
    {
        if ($this->accesos->removeElement($acceso)) {
            // set the owning side to null (unless already changed)
            if ($acceso->getPlanillaEquipo() === $this) {
                $acceso->setPlanillaEquipo(null);
            }
        }

        return $this;
    }
}