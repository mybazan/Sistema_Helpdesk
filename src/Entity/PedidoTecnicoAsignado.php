<?php

namespace App\Entity;

use App\Repository\PedidoTecnicoAsignadoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedidoTecnicoAsignadoRepository::class)
 */
class PedidoTecnicoAsignado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pedido::class, inversedBy="pedidoTecnicoAsignados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pedido;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pedidoTecnicoAsignados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tecnicoAsignado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaAsignacion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuarioAsignacion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $esOperativo;
    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTecnicoAsignado(): ?User
    {
        return $this->tecnicoAsignado;
    }

    public function setTecnicoAsignado(?User $tecnicoAsignado): self
    {
        $this->tecnicoAsignado = $tecnicoAsignado;

        return $this;
    }

    public function getFechaAsignacion(): ?\DateTimeInterface
    {
        return $this->fechaAsignacion;
    }

    public function setFechaAsignacion(\DateTimeInterface $fechaAsignacion): self
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    public function getUsuarioAsignacion(): ?User
    {
        return $this->usuarioAsignacion;
    }

    public function setUsuarioAsignacion(?User $usuarioAsignacion): self
    {
        $this->usuarioAsignacion = $usuarioAsignacion;

        return $this;
    }

    public function getEsOperativo(): ?bool
    {
        return $this->esOperativo;
    }

    public function setEsOperativo(bool $esOperativo): self
    {
        $this->esOperativo = $esOperativo;

        return $this;
    }
}
