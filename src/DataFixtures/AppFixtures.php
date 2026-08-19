<?php

namespace App\DataFixtures;

use App\Entity\PedidoEstado;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\PermisoRepository;
use App\Repository\RoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $permisoRepository;
    private $roleRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, PermisoRepository $permisoRepository, RoleRepository $roleRepository)
    {
        $this->encoder = $userPasswordEncoder;
        $this->permisoRepository = $permisoRepository;
        $this->roleRepository = $roleRepository;
    }

    public function load(ObjectManager $manager)
    {
        $estadosPedido = [
            'Recibido',
            'Asignado',
            'Pendiente',
            'En Proceso',
            'Resuelto',
            'Finalizado',
            'Demorado',
            'Desestimado',
        ];

        foreach ($estadosPedido as $nombreEstado) {
            if (!$manager->getRepository(PedidoEstado::class)->findOneBy(['nombre' => $nombreEstado])) {
                $estado = new PedidoEstado();
                $estado->setNombre($nombreEstado);
                $estado->setIsActive(true);
                $manager->persist($estado);
            }
        }
        $manager->flush();

        /* Crea el rol superuser */
        $roles = [
            "ROLE_SUPERUSER" => "Super Admin"
        ];
        foreach ($roles as $key => $value) {
            if (!$manager->getRepository(Role::class)->findByRoleName([$key])) {
                $role = new Role();
                $role->setRoleName($key);
                $manager->persist($role);
                $manager->flush();
            }
        }

        /* Le asigna los permisos básicos al rol superuser */
        $rol = $this->roleRepository->findByName("ROLE_SUPERUSER");
        $permisos = array("ADMINISTRACION", "VER_INICIO", "ROLES_VER", "ROLES_EDITAR");
        $this->permisoRepository->asignarRoles($rol, $permisos);

        /* Crea el usuario admin con el rol superuser */
        $user = new User();
        if (!$manager->find(User::class, 1)) {
            $user->setUsername('maxi');
            $user->setRoles(["ROLE_SUPERUSER"]);
            $user->setPassword($this->encoder->encodePassword($user, 'maxi'));
            $user->setNombre('Maximiliano');
            $user->setApellido('Maxi Bazan');
            $user->setEmail('maxibazan@gmail.com');
            $user->setSuspended(false);
            $user->setDeleted(false);
            $user->setIsTecnico(false);
            $manager->persist($user);

            $manager->flush();
        }
    }
}
