<?php

namespace App\Repository;

use App\Entity\Permiso;
use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PermisoRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Permiso::class);
        $this->entityManager = $entityManager;
    }

    /* public function findForActionIndex($filtro = [])
    {
      $qb = $this->createQueryBuilder('e')
            ->orderBy("e.id", "ASC");

      if(isset($filtro["nombre"]) && $filtro["nombre"] != '') {
        $qb
          ->andWhere("e.nombre LIKE :nombre")
          ->setParameter("nombre", '%'.$filtro["nombre"].'%')
        ;
      }

      return $qb;
    } */

    public function findByName($nombre)
    {
      $qb = $this->createQueryBuilder('e')
        ->andWhere("e.nombre = :nombre")
        ->setParameter("nombre", $nombre)
      ;

      return $qb->getQuery()->getOneOrNullResult();
    }

    public function asignarRoles($role, $permisos){
      $result = true;
      foreach($permisos as $permiso){
        try{
          $newPermiso = New Permiso;
          $newPermiso->setNombre($permiso);
          $newPermiso->setRole($role);
          $this->entityManager->persist($newPermiso);
          $this->entityManager->flush();
        }catch(\Exception $e){
          $result = false;
        }
      }

      return $result;
    }

    public function delete(Role $role){
      try {
        $qb = $this->createQueryBuilder('p')
          ->delete()
          ->where('p.role = :role')
          ->setParameter('role', $role)
          ->getQuery()->execute();
        return true;
      } catch (\Exception $e) {
          return $e;
      }
    }

    public function permisosDeUnRol(Role $role){
      $qb = $this->createQueryBuilder('e')
        ->andWhere("e.role = :role")
        ->setParameter("role", $role);
      
      return $qb->getQuery()
        ->getResult();
    }
}
