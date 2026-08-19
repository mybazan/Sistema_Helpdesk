<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedidoEquipoRepository::class)
 */
class PedidoEquipo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pedido", inversedBy="pedidoEquipos")
     * 
     */
    private $pedido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipo")
     * 
     */
    private $equipo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaInicio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaFin;
    
    
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
    
    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): self
    {
        $this->equipo = $equipo;
        
        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(?\DateTimeInterface $fechaInicio): self
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
}
