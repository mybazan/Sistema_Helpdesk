<?php

namespace App\Entity;

use App\Repository\PedidoHistorialEstadoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedidoHistorialEstadoRepository::class)
 */
class PedidoHistorialEstado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PedidoEstado::class, inversedBy="pedidoHistorialEstados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pedidoEstado;

    /**
     * @ORM\ManyToOne(targetEntity=Pedido::class, inversedBy="pedidoHistorialEstados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pedido;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pedidoHistorialEstados")
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedidoEstado(): ?PedidoEstado
    {
        return $this->pedidoEstado;
    }

    public function setPedidoEstado(?PedidoEstado $pedidoEstado): self
    {
        $this->pedidoEstado = $pedidoEstado;

        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(?Pedido $pedido): self
    {
        $this->pedido = $pedido;

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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
