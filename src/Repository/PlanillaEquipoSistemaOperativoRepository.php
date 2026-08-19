<?php

namespace App\Repository;

use App\Entity\PlanillaEquipoSistemaOperativo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanillaEquipoSistemaOperativo>
 *
 * @method PlanillaEquipoSistemaOperativo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanillaEquipoSistemaOperativo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanillaEquipoSistemaOperativo[]    findAll()
 * @method PlanillaEquipoSistemaOperativo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanillaEquipoSistemaOperativoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanillaEquipoSistemaOperativo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PlanillaEquipoSistemaOperativo $entity, bool $flush = true): void
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
    public function remove(PlanillaEquipoSistemaOperativo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PlanillaEquipoSistemaOperativo[] Returns an array of PlanillaEquipoSistemaOperativo objects
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
    public function findOneBySomeField($value): ?PlanillaEquipoSistemaOperativo
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
