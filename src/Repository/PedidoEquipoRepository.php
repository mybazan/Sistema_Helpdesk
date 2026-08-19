<?php

namespace App\Repository;

use App\Entity\PedidoEquipo;
use App\Entity\PedidoHistorialEstado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method PedidoEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PedidoEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PedidoEquipo[]    findAll()
 * @method PedidoEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PedidoEquipo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PedidoEquipo $entity, bool $flush = true): void
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
    public function remove(PedidoEquipo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param int $pedidoId
     * @return PedidoEquipo[]
     */
    public function findByPedidoId(int $pedidoId)
    {
        return $this->createQueryBuilder('pe')
            ->andWhere('pe.pedido = :pedidoId')
            ->setParameter('pedidoId', $pedidoId)
            ->getQuery()
            ->getResult();
    }
    
    // /**
    //  * @return PedidoEquipo[] Returns an array of PedidoEquipo objects
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
    public function findOneBySomeField($value): ?PedidoEquipo
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
