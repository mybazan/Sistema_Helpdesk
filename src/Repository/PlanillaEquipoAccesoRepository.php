<?php

namespace App\Repository;

use App\Entity\PlanillaEquipoAcceso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanillaEquipoAcceso>
 *
 * @method PlanillaEquipoAcceso|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanillaEquipoAcceso|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanillaEquipoAcceso[]    findAll()
 * @method PlanillaEquipoAcceso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanillaEquipoAccesoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanillaEquipoAcceso::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PlanillaEquipoAcceso $entity, bool $flush = true): void
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
    public function remove(PlanillaEquipoAcceso $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PlanillaEquipoAcceso[] Returns an array of PlanillaEquipoAcceso objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanillaEquipoAcceso
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
