<?php

namespace App\Repository;

use App\Entity\TipoEquipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoEquipo>
 *
 * @method TipoEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoEquipo[]    findAll()
 * @method TipoEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoEquipo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TipoEquipo $entity, bool $flush = true): void
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
    public function remove(TipoEquipo $entity, bool $flush = true): void
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
    //  * @return TipoEquipo[] Returns an array of TipoEquipo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipoEquipo
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
