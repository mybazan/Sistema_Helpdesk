<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use App\Services\TextFormatter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PedidoRepository::class)
 */
class Pedido
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pedidos")
     */
    private $tecnicoAsignado;

    /**
     * @ORM\ManyToOne(targetEntity=Ubicacion::class)
     * @ORM\JoinColumn(name="ubicacion_id", referencedColumnName="id", nullable=true)
     */
    private $ubicacionPedido;

    /**
     * @ORM\ManyToOne(targetEntity=Personal::class, inversedBy="pedidos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $solicitante;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $solicitanteTexto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubicacionTexto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $solicitud;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $solucion;

    /**
     * @ORM\Column(type="integer")
     */
    private $prioridad;

    /**
     * @ORM\OneToMany(targetEntity=PedidoHistorialEstado::class, mappedBy="pedido", cascade={"remove"})
     */
    private $pedidoHistorialEstados;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion; 

    /**
     * @ORM\OneToMany(targetEntity=PedidoEquipo::class, mappedBy="pedido", cascade={"remove"})
     */
    private $pedidoEquipos;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $solucionAdjunto;

    /**
     * @ORM\OneToMany(targetEntity=PedidoTecnicoAsignado::class, mappedBy="pedido")
     */
    private $pedidoTecnicoAsignados; 


    public function __construct()
    {
        $this->pedidoHistorialEstados = new ArrayCollection();
        $this->pedidoTecnicoAsignados = new ArrayCollection();
        $this->pedidoEquipos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTecnicoAsignado(): ?User
    {
        return $this->tecnicoAsignado;
    }

    public function setTecnicoAsignado(?User $tecnicoAsignado): self
    {
        $this->tecnicoAsignado = $tecnicoAsignado;

        return $this;
    }

    public function getUbicacionPedido(): ?Ubicacion
    {
        return $this->ubicacionPedido;
    }

    public function setUbicacionPedido(?Ubicacion $ubicacionPedido): self
    {
        $this->ubicacionPedido = $ubicacionPedido;

        return $this;
    }

    public function getSolicitante(): ?Personal
    {
        return $this->solicitante;
    }

    public function setSolicitante(?Personal $solicitante): self
    {
        $this->solicitante = $solicitante;

        return $this;
    }

    public function getSolicitanteTexto(): ?string
    {
        return $this->solicitanteTexto;
    }

    public function setSolicitanteTexto(?string $solicitanteTexto): self
    {
        $this->solicitanteTexto = TextFormatter::formatPlainText($solicitanteTexto);

        return $this;
    }

    public function getUbicacionTexto(): ?string
    {
        return $this->ubicacionTexto;
    }

    public function setUbicacionTexto(?string $ubicacionTexto): self
    {
        $this->ubicacionTexto = TextFormatter::formatPlainText($ubicacionTexto);

        return $this;
    }

    public function getSolicitanteDisplay(): string
    {
        if ($this->solicitanteTexto) {
            return $this->solicitanteTexto;
        }

        if ($this->solicitante) {
            return sprintf('%s, %s', $this->solicitante->getApellido(), $this->solicitante->getNombre());
        }

        return '-';
    }

    public function getUbicacionDisplay(): string
    {
        if ($this->ubicacionTexto) {
            return $this->ubicacionTexto;
        }

        if ($this->ubicacionPedido) {
            return $this->ubicacionPedido->getNomenclatura();
        }

        return '-';
    }

    public function getSolicitud(): ?string
    {
        return $this->solicitud;
    }

    public function setSolicitud(string $solicitud): self
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    public function getSolucion(): ?string
    {
        return $this->solucion;
    }

    public function setSolucion(?string $solucion): self
    {
        $this->solucion = $solucion;

        return $this;
    }

    public function getPrioridad(): ?string
    {
        return $this->prioridad;
    }

    public function setPrioridad(?string $prioridad): self
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * @return Collection<int, PedidoHistorialEstado>
     */
    public function getPedidoHistorialEstados(): Collection
    {
        return $this->pedidoHistorialEstados;
    }

    public function getEstadoActual(): ?PedidoHistorialEstado
    {
        $historial = $this->pedidoHistorialEstados->toArray();
        if ($historial === []) {
            return null;
        }

        usort($historial, static fn (PedidoHistorialEstado $a, PedidoHistorialEstado $b) => $b->getFecha() <=> $a->getFecha());

        return $historial[0];
    }

    public function getEstadoActualNombre(): ?string
    {
        $estadoActual = $this->getEstadoActual();
        if (!$estadoActual || !$estadoActual->getPedidoEstado()) {
            return null;
        }

        return $estadoActual->getPedidoEstado()->getNombre();
    }

    /**
     * @return PedidoHistorialEstado[]
     */
    public function getPedidoHistorialEstadosOrdenados(): array
    {
        $historial = $this->pedidoHistorialEstados->toArray();
        usort($historial, static fn (PedidoHistorialEstado $a, PedidoHistorialEstado $b) => $a->getFecha() <=> $b->getFecha());

        return $historial;
    }

    public function addPedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if (!$this->pedidoHistorialEstados->contains($pedidoHistorialEstado)) {
            $this->pedidoHistorialEstados[] = $pedidoHistorialEstado;
            $pedidoHistorialEstado->setPedido($this);
        }
        return $this;
    }

    public function removePedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if ($this->pedidoHistorialEstados->removeElement($pedidoHistorialEstado)) {
            // set the owning side to null (unless already changed)
            if ($pedidoHistorialEstado->getPedido() === $this) {
                $pedidoHistorialEstado->setPedido(null);
            }
        }

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

    /**
     * @return Collection<int, PedidoEquipo>
     */
    public function getPedidoEquipos(): Collection
    {
        return $this->pedidoEquipos;
    }

    public function addPedidoEquipo(PedidoEquipo $pedidoEquipo): self
    {
        if (!$this->pedidoEquipos->contains($pedidoEquipo)) {
            $this->pedidoEquipos[] = $pedidoEquipo;
            $pedidoEquipo->setPedido($this);
        }

        return $this;
    }

    public function removePedidoEquipo(PedidoEquipo $pedidoEquipo): self
    {
        if ($this->pedidoEquipos->removeElement($pedidoEquipo)) {
            // set the owning side to null (unless already changed)
            if ($pedidoEquipo->getPedido() === $this) {
                $pedidoEquipo->setPedido(null);
            }
        }

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getSolucionAdjunto(): ?string
    {
        return $this->solucionAdjunto;
    }

    public function setSolucionAdjunto(?string $solucionAdjunto): self
    {
        $this->solucionAdjunto = $solucionAdjunto;

        return $this;
    }

    /**
     * @return Collection<int, PedidoTecnicoAsignado>
     */
    public function getPedidoTecnicoAsignados(): Collection
    {
        return $this->pedidoTecnicoAsignados;
    }

    public function addPedidoTecnicoAsignado(PedidoTecnicoAsignado $pedidoTecnicoAsignado): self
    {
        if (!$this->pedidoTecnicoAsignados->contains($pedidoTecnicoAsignado)) {
            $this->pedidoTecnicoAsignados[] = $pedidoTecnicoAsignado;
            $pedidoTecnicoAsignado->setPedido($this);
        }

        return $this;
    }

    public function removePedidoTecnicoAsignado(PedidoTecnicoAsignado $pedidoTecnicoAsignado): self
    {
        if ($this->pedidoTecnicoAsignados->removeElement($pedidoTecnicoAsignado)) {
            // set the owning side to null (unless already changed)
            if ($pedidoTecnicoAsignado->getPedido() === $this) {
                $pedidoTecnicoAsignado->setPedido(null);
            }
        }

        return $this;
    }
}
