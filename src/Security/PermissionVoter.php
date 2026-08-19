<?php


namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use App\Repository\PermisoRepository;
use App\Repository\RoleRepository;

class PermissionVoter extends Voter
{
    // Voter de secciones
    // Añadir las secciones para tener los voters con el tipo: SECCION_PERMISO
    // Ejemplo: USERS_EDITAR
    const secciones = [
        'ROLES',
        'USERS',
        'EQUIPO',
        'PERSONAL',
        'CARACTERISTICA',
        'TICKET',
        'TIPO_EQUIPO',
        'UBICACION',
    ];

    /* Voter generales */
    const generales = [
        'VER',
        'CREAR',
        'EDITAR',
        'ELIMINAR',
        'ESTADISTICAS',
        'RENDIR',
        'ASIGNAR',
        'IMPRIMIR',

    ];

    /* Voter específicos de usuarios */
    const especificos = [
        'VER_INICIO',
        'USERS_ACTIVAR',
        'USERS_PASSWORD',
    ];

    private $permisoRepository;
    private $roleRepository;

    public function __construct(PermisoRepository $permisoRepository, RoleRepository $roleRepository)
    {
        $this->permisoRepository = $permisoRepository;
        $this->roleRepository = $roleRepository;
    }

    protected function supports($attribute, $subject)
    {
        $arrayFinal = [];
        // Agregamos las secciones generales
        foreach(self::secciones as $seccion){
            foreach(self::generales as $permiso){
                array_push($arrayFinal, $seccion . '_' . $permiso);
            }            
        }
        // Agregamos las secciones específicas.
        foreach(self::especificos as $permiso){
            array_push($arrayFinal, $permiso);
        }
        
        // Si el atributo no es uno de los que soportamos, devolver false.
        if (!in_array($attribute, $arrayFinal)){
            return false;
        }

        return true;
    }

    // $Attribute es el nombre (las constantes) y $subject es un objeto que se puede usar
    // para validar si el usuario puede o no realizar ese attribute en el subject
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // El usuario debe estar logeado; sino, deniega el acceso.
            return false;
        }

        return $this->puedeONo($user->getRoles(), strtoupper($attribute));
    }

    private function puedeONo($rolArray, $permiso)
    {
        foreach ($rolArray as $rol) {
            $rolEntidad = $this->roleRepository->findByName($rol);
            if (!$rolEntidad) {
                continue;
            }

            $permisos = $this->permisoRepository->permisosDeUnRol($rolEntidad);
            if ($this->verificarSiContiene($permisos, $permiso)) {
                return true;
            }
        }

        return false;
    }

    private function verificarSiContiene($array, $rolString){
        foreach ($array as $permiso) {
            if ($permiso instanceof \App\Entity\Permiso && $permiso->getNombre() === $rolString) {
                return true;
            }
            if (is_string($permiso) && $permiso === $rolString) {
                return true;
            }
        }

        return false;
    }
}