<?php

namespace App\Entity;

use App\Repository\PersonalRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=PersonalRepository::class)
 */
class Personal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.", groups={"registro"})
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.", groups={"registro"})
     */
    private $apellido;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.", groups={"registro"})
     * @Assert\Email(message="Formato de correo electrónico inválido.", groups={"registro"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, nullable=true, unique=true )
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     match=true,
     *     message="El campo solo puede contener números.",
     *     groups={"registro"}
     * )
     * @Assert\Length(
     *      min = 7,
     *      max = 9,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números.",
     *      groups={"registro"}
     * )
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     match=true,
     *     message="El campo solo puede contener números.",
     *     groups={"registro"}
     * )
     * @Assert\Length(
     *      min = 10,
     *      max = 12,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números.",
     *      groups={"registro"}
     * )
     */
    private $cuil;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Regex(
     *      pattern="/[0-9]/",
     *      match=true,
     *      message="El campo solo puede contener números.",
     *      groups={"registro"}
     * )
     * @Assert\Length(
     *      min = 10,
     *      max = 15,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números.",
     *      groups={"registro"}
     * )
     */
    private $telefono;

    /**
     * @ORM\Column(type="boolean")
     */
    private $suspended;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted;
    
    /**
     * @ORM\ManyToOne(targetEntity=Ubicacion::class)
     * @ORM\JoinColumn(name="ubicacion_id", referencedColumnName="id", nullable=true)
     */
    private $ubicacion;

    /**
     * @ORM\OneToMany(targetEntity=UsuarioEquipo::class, mappedBy="usuario")
     */
    private $equipos;

    /**
      * @ORM\Column(type="datetime")
      * @Gedmo\Timestampable(on="create")
    */
    private $createdAt;

    /**
        * @ORM\Column(type="datetime")
        * @Gedmo\Timestampable(on="update")
    */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Pedido::class, mappedBy="solicitante")
     */
    private $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
        $this->equipos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNombreCompletoFormal();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    { 
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . ', ' . $this->apellido;
    }

    public function getNombreCompletoFormal(): string
    {
        return $this->apellido . ', ' . $this->nombre;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getCuil(): ?string
    {
        return $this->cuil;
    }

    public function setCuil(?string $cuil): self
    {
        $this->cuil = $cuil;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getSuspended(): ?bool
    {
        return $this->suspended;
    }

    public function setSuspended(bool $suspended): self
    {
        $this->suspended = $suspended;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    /**
     * @return Collection<int, UsuarioEquipo>
     */
    public function getEquipos(): Collection
    {
        return $this->equipos;
    }

    public function addEquipo(UsuarioEquipo $equipo): self
    {
        if (!$this->equipos->contains($equipo)) {
            $this->equipos[] = $equipo;
            $equipo->setUsuario($this);
        }

        return $this;
    }

    public function removeEquipo(UsuarioEquipo $equipo): self
    {
        if ($this->equipos->removeElement($equipo)) {
            // set the owning side to null (unless already changed)
            if ($equipo->getUsuario() === $this) {
                $equipo->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pedido>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedido $pedido): self
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos[] = $pedido;
            $pedido->setSolicitante($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getSolicitante() === $this) {
                $pedido->setSolicitante(null);
            }
        }

        return $this;
    }

}