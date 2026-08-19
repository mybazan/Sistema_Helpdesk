<?php

namespace App\Repository;

use App\Entity\PedidoTecnicoAsignado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PedidoTecnicoAsignado>
 *
 * @method PedidoTecnicoAsignado|null find($id, $lockMode = null, $lockVersion = null)
 * @method PedidoTecnicoAsignado|null findOneBy(array $criteria, array $orderBy = null)
 * @method PedidoTecnicoAsignado[]    findAll()
 * @method PedidoTecnicoAsignado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoTecnicoAsignadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PedidoTecnicoAsignado::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PedidoTecnicoAsignado $entity, bool $flush = true): void
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
    public function remove(PedidoTecnicoAsignado $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PedidoTecnicoAsignado[] Returns an array of PedidoTecnicoAsignado objects
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
    public function findOneBySomeField($value): ?PedidoTecnicoAsignado
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
