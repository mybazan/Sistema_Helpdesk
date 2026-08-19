<?php

namespace App\Repository;

use App\Entity\PedidoEstado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PedidoEstado>
 *
 * @method PedidoEstado|null find($id, $lockMode = null, $lockVersion = null)
 * @method PedidoEstado|null findOneBy(array $criteria, array $orderBy = null)
 * @method PedidoEstado[]    findAll()
 * @method PedidoEstado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoEstadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PedidoEstado::class);
    }

    public function findForActionIndex($filtros = []){
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.id','ASC');

        if(isset($filtros["nombre"]) && $filtros["nombre"] != ''){
            $qb
                ->andWhere("p.nombre LIKE :nombre")
                ->setParameter("nombre",'%'.$filtros["nombre"].'%');
        }
        return $qb;
    }

    public function findOneByNombre($value): ?PedidoEstado
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nombre = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PedidoEstado $entity, bool $flush = true): void
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
    public function remove(PedidoEstado $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PedidoEstado[] Returns an array of PedidoEstado objects
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
    public function findOneBySomeField($value): ?PedidoEstado
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
