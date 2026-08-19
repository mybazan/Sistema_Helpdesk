<?php

namespace App\Repository;

use App\Entity\PedidoHistorialEstado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PedidoHistorialEstado>
 *
 * @method PedidoHistorialEstado|null find($id, $lockMode = null, $lockVersion = null)
 * @method PedidoHistorialEstado|null findOneBy(array $criteria, array $orderBy = null)
 * @method PedidoHistorialEstado[]    findAll()
 * @method PedidoHistorialEstado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoHistorialEstadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PedidoHistorialEstado::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PedidoHistorialEstado $entity, bool $flush = true): void
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
    public function remove(PedidoHistorialEstado $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findFechaByPedidoAndEstado($pedidoId, $estadoNombre)
    {
        return $this->createQueryBuilder('phe')
            ->innerJoin('phe.pedidoEstado', 'pe')
            ->andWhere('phe.pedido = :pedidoId')
            ->andWhere('pe.nombre = :estadoNombre')
            ->setParameter('pedidoId', $pedidoId)
            ->setParameter('estadoNombre', $estadoNombre)
            ->orderBy('phe.fecha', 'DESC') // Opcional, por si hay varios cambios y quieres el más reciente
            ->getQuery()
            ->getOneOrNullResult();
    }
    // /**
    //  * @return PedidoHistorialEstado[] Returns an array of PedidoHistorialEstado objects
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
    public function findOneBySomeField($value): ?PedidoHistorialEstado
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
