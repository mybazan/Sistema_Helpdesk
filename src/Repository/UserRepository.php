<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    private $entityManager;
    private $security;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Security $security)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function saveUser($user):User
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function findOneByUsernameOrEmail($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val or u.username = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findForActionIndex($filtro = [])
    {      
      $qb = $this->createQueryBuilder('e');
      if (!$this->security->getUser()->isSuperAdmin()){
        $qb
        ->andWhere("e.roles NOT LIKE :AdminRole")
        ->setParameter("AdminRole", '%ROLE_SUPERUSER%');
      }
      if(isset($filtro["nombre"]) && $filtro["nombre"] != '') {
        $qb
          ->andWhere("e.nombre LIKE :nombre")
          ->setParameter("nombre", '%'.$filtro["nombre"].'%')
        ;
      }
      if(isset($filtro["apellido"]) && $filtro["apellido"] != '') {
        $qb
          ->andWhere("e.apellido LIKE :apellido")
          ->setParameter("apellido", '%'.$filtro["apellido"].'%')
        ;
      }
      if(isset($filtro["username"]) && $filtro["username"] != '') {
        $qb
          ->andWhere("e.username LIKE :username")
          ->setParameter("username", '%'.$filtro["username"].'%')
        ;
      }
      return $qb;
    }

    public function delete(User $user){
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function changeValidite(User $user){
      if ($user->getSuspended())
          $user->setSuspended(false);
      else
          $user->setSuspended(true);
      $this->entityManager->persist($user);
      $this->entityManager->flush();
      return $user;
    }
    
    public function findRepetido($value): ?User
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

    public function findTecnicos(){
      return $this->createQueryBuilder('u')
                  ->andWhere('u.isTecnico = true')
                  ->andWhere('u.suspended = false')
                  ->andWhere('u.deleted = false')
                  ->orderBy('u.apellido')
                  ->getQuery()
                  ->getResult();
    }

    public function findUserById($id){
      return $this->createQueryBuilder('u')
                  ->andWhere('u.id = :id')
                  ->setParameter("id", $id)
                  ->getQuery()
                  ->getOneOrNullResult();
    }

    public function findTecnicosAsignadosPedido($valores){
      $qb = $this->createQueryBuilder('e');

      if(!empty($valores)) {
        $qb->Where("e.id IN (".$valores.")");
      }else{
        return null;
      }
      
      return $qb->getQuery()->getResult();
    }
}
