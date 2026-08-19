<?php

namespace App\Repository;

use App\Entity\CaracteristicaTipoEquipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaracteristicaTipoEquipo>
 *
 * @method CaracteristicaTipoEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaracteristicaTipoEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaracteristicaTipoEquipo[]    findAll()
 * @method CaracteristicaTipoEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaracteristicaTipoEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaracteristicaTipoEquipo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CaracteristicaTipoEquipo $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CaracteristicaTipoEquipo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findForActionIndex($filtros = []){
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id','ASC');

        if(isset($filtros["nombre"]) && $filtros["nombre"] != ''){
            $qb
                ->andWhere("t.nombre LIKE :nombre")
                ->setParameter("nombre",'%'.$filtros["nombre"].'%');
        }
        return $qb;
    }  

    // /**
    //  * @return CaracteristicaTipoEquipo[] Returns an array of CaracteristicaTipoEquipo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CaracteristicaTipoEquipo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
