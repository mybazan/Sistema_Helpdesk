<?php

namespace App\Repository;

use App\Entity\PlanillaEquipoAlmacenamiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanillaEquipoAlmacenamiento>
 *
 * @method PlanillaEquipoAlmacenamiento|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanillaEquipoAlmacenamiento|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanillaEquipoAlmacenamiento[]    findAll()
 * @method PlanillaEquipoAlmacenamiento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanillaEquipoAlmacenamientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanillaEquipoAlmacenamiento::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PlanillaEquipoAlmacenamiento $entity, bool $flush = true): void
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
    public function remove(PlanillaEquipoAlmacenamiento $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Almacenamiento[] Returns an array of Almacenamiento objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Almacenamiento
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
