<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields={"username"},
 *      errorPath="username",
 *      message="El usuario ingresado ya se encuentra registrado."
 * )
 * @UniqueEntity(
 *      fields={"email"},
 *      errorPath="email",
 *      message="El correo electrónico ingresado ya se encuentra registrado."
 * )
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     * @Assert\NotBlank( message="El campo no puede estar vacío")
     */
    private $username;

    /**
     * @ORM\Column(type="json", nullable=false)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.")
     */
    private $apellido;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="El campo no puede estar vacío.")
     * @Assert\Email(message="Formato de correo electrónico inválido.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     match=true,
     *     message="El campo solo puede contener números."
     * )
     * @Assert\Length(
     *      min = 7,
     *      max = 9,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números."
     * )
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     match=true,
     *     message="El campo solo puede contener números."
     * )
     * @Assert\Length(
     *      min = 10,
     *      max = 12,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números."
     * )
     */
    private $cuil;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Regex(
     *     pattern="/[0-9]/",
     *     match=true,
     *     message="El campo solo puede contener números."
     * )
     * @Assert\Length(
     *      min = 10,
     *      max = 15,
     *      minMessage="El campo debe contener como mínimo {{ limit }} números.",
     *      maxMessage="El campo debe contener como máximo {{ limit }} números."
     * )
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $suspended;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    /**
     * @ORM\Column(type="string", length=255))
     */
    private $password;   

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users", cascade={"persist"})
    */
    protected $rolActual;

    /**
     * @ORM\ManyToOne(targetEntity=Ubicacion::class)
     * @ORM\JoinColumn(name="ubicacion_id", referencedColumnName="id", nullable=true)
     */
    private $ubicacion;

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
     * @ORM\OneToMany(targetEntity=Pedido::class, mappedBy="tecnicoAsignado")
     */
    private $pedidos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTecnico;

    /**
     * @ORM\OneToMany(targetEntity=PedidoHistorialEstado::class, mappedBy="usuario")
     */
    private $pedidoHistorialEstados;

    /**
     * @ORM\OneToMany(targetEntity=PedidoTecnicoAsignado::class, mappedBy="tecnicoAsignado")
     */
    private $pedidoTecnicoAsignados;

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->nombre = trim(strtoupper($this->nombre));
        $this->apellido = trim(strtoupper($this->apellido));
        $this->direccion = trim(strtoupper($this->direccion));
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->nombre = trim(strtoupper($this->nombre));
        $this->apellido = trim(strtoupper($this->apellido));
        $this->direccion = trim(strtoupper($this->direccion));
    }

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
        $this->pedidoHistorialEstados = new ArrayCollection();
        $this->pedidoTecnicoAsignados = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }   

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail( $email): self
    {
        $this->email = $email;

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

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }
    
    public function __toString()
    {
        $fullName = $this->apellido.', '.$this->nombre;
        return $fullName;
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User)
        return !$this->getSuspended() && !$this->isDeleted() && $this->getPassword() == $user->getPassword() && $this->getUsername() == $user->getUsername()
            && $this->getEmail() == $user->getEmail() ;
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

    public function getDni()
    {
        return $this->dni;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    public function getCuil()
    {
        return $this->cuil;
    }

    public function setCuil($cuil)
    {
        $this->cuil = $cuil;

        return $this;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function isSuperAdmin(){
        return $this->roles[0] == 'ROLE_SUPERUSER' ? true : false;
    }

    public function getRolActual(): ?Role
    {
        return $this->rolActual;
    }

    public function setRolActual(?Role $rolActual): self
    {
        $this->rolActual = $rolActual;

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
            $pedido->setTecnicoAsignado($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getTecnicoAsignado() === $this) {
                $pedido->setTecnicoAsignado(null);
            }
        }

        return $this;
    }

    public function getIsTecnico(): ?bool
    {
        return $this->isTecnico;
    }

    public function setIsTecnico(bool $isTecnico): self
    {
        $this->isTecnico = $isTecnico;

        return $this;
    }

    /**
     * @return Collection<int, PedidoHistorialEstado>
     */
    public function getPedidoHistorialEstados(): Collection
    {
        return $this->pedidoHistorialEstados;
    }

    public function addPedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if (!$this->pedidoHistorialEstados->contains($pedidoHistorialEstado)) {
            $this->pedidoHistorialEstados[] = $pedidoHistorialEstado;
            $pedidoHistorialEstado->setUsuario($this);
        }

        return $this;
    }

    public function removePedidoHistorialEstado(PedidoHistorialEstado $pedidoHistorialEstado): self
    {
        if ($this->pedidoHistorialEstados->removeElement($pedidoHistorialEstado)) {
            // set the owning side to null (unless already changed)
            if ($pedidoHistorialEstado->getUsuario() === $this) {
                $pedidoHistorialEstado->setUsuario(null);
            }
        }

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
            $pedidoTecnicoAsignado->setTecnicoAsignado($this);
        }

        return $this;
    }

    public function removePedidoTecnicoAsignado(PedidoTecnicoAsignado $pedidoTecnicoAsignado): self
    {
        if ($this->pedidoTecnicoAsignados->removeElement($pedidoTecnicoAsignado)) {
            // set the owning side to null (unless already changed)
            if ($pedidoTecnicoAsignado->getTecnicoAsignado() === $this) {
                $pedidoTecnicoAsignado->setTecnicoAsignado(null);
            }
        }

        return $this;
    }
}