<?php

namespace App\Repository;

use App\Entity\Personal;
use Symfony\Component\Security\Core\Security;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Personal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personal[]    findAll()
 * @method Personal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalRepository extends ServiceEntityRepository
{
    private $entityManager;
    private $security;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Security $security)
    {
      parent::__construct($registry, Personal::class);
      $this->entityManager = $entityManager;
      $this->security = $security;
    }

    public function savePersonal($user):Personal
    {
      $this->entityManager->persist($user);
      $this->entityManager->flush();
      return $user;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Personal $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOneByPersonalnameOrEmail($value): ?Personal
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val or u.username = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findForActionIndex($filtro = [])
    {
      $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.ubicacion', 'u')
            ->addOrderBy("c.apellido", "ASC");

      if(isset($filtro["nombre"]) && $filtro["nombre"] != '') {
        $qb
          ->andWhere("c.nombre LIKE :nombre")
          ->setParameter("nombre", '%'.$filtro["nombre"].'%')
        ;
      }
      if(isset($filtro["apellido"]) && $filtro["apellido"] != '') {
        $qb
          ->andWhere("c.apellido LIKE :apellido")
          ->setParameter("apellido", '%'.$filtro["apellido"].'%')
        ;
      }
      if(isset($filtro["dni"]) && $filtro["dni"] != '') {
        $qb
          ->andWhere("c.dni LIKE :dni")
          ->setParameter("dni", '%'.$filtro["dni"].'%')
        ;
      }
      if(isset($filtro["ubicacion"]) && $filtro["ubicacion"] != '') {
        $qb
          ->andWhere("c.ubicacion = :ubicacion")
          ->setParameter("ubicacion", $filtro["ubicacion"])
        ;
      }
      return $qb;
    }

    public function delete(Personal $user){
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function changeValidite(Personal $user){
      if ($user->getSuspended())
          $user->setSuspended(false);
      else
          $user->setSuspended(true);
      $this->entityManager->persist($user);
      $this->entityManager->flush();
      return $user;
    }
    
    public function findRepetido($value): ?Personal
    {
      $email = $value->getEmail();
      $id = $value->getId();
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->andWhere('u.id != :id')
            ->setParameter('email', $email)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}