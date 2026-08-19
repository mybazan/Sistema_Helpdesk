<?php

namespace App\Repository;

use App\Entity\Equipo;
use App\Entity\EquipoHistorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipoHistorial>
 *
 * @method EquipoHistorial|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipoHistorial|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipoHistorial[]    findAll()
 * @method EquipoHistorial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipoHistorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipoHistorial::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(EquipoHistorial $entity, bool $flush = true): void
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
    public function remove(EquipoHistorial $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findForActionIndex($filtro = [])
    {
        $qb = $this->createQueryBuilder('eh')
            ->addOrderBy("eh.id", "DESC");

        return $qb;
    }

    public function findUltimoHistorial(Equipo $equipo, ?bool $esUbicacion): ?EquipoHistorial
    {
        $qb = $this->createQueryBuilder('eh')
            ->andWhere('eh.equipo = :equipo')
            ->andWhere('eh.fechaFin IS NULL')
            ->setParameter('equipo', $equipo)
            ->orderBy('eh.fechaInicio', 'DESC')
            ->setMaxResults(1);

        if ($esUbicacion !== null) {
            $qb->andWhere('eh.esUbicacion = :esUbicacion')
                ->setParameter('esUbicacion', $esUbicacion);
        } else {
            $qb->andWhere('eh.esUbicacion IS NULL'); // Para el primer registro sin definir
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findForUbicaciones($filtro = [], Equipo $equipo)
    {
        $qb = $this->createQueryBuilder('eh')
            ->where('eh.esUbicacion = :esUbicacion')
            ->orWhere('eh.esUbicacion IS NULL')
            ->andWhere('eh.equipo = :equipo') // Filtro por equipo
            ->setParameter('esUbicacion', true)
            ->setParameter('equipo', $equipo) // Asignar parámetro del equipo
            ->orderBy('eh.fechaInicio', 'DESC');

        if (!empty($filtro['fechaInicio'])) {
            $qb->andWhere('(eh.fechaInicio >= :fechaInicio OR eh.fechaInicio IS NULL)')
                ->setParameter('fechaInicio', $filtro['fechaInicio']);
        }

        // Filtro por fechaFin (permitiendo valores NULL)
        if (!empty($filtro['fechaFin'])) {
            $qb->andWhere('(eh.fechaFin <= :fechaFin OR eh.fechaFin IS NULL)')
                ->setParameter('fechaFin', $filtro['fechaFin']);
        }
        if (!empty($filtro['ubicacion'])) {
            $qb->andWhere('eh.ubicacion = :ubicacion')
                ->setParameter('ubicacion', $filtro['ubicacion']);
        }

        return $qb->getQuery();
    }

    public function findForIpYHost($filtro = [], $equipo)
    {
        $qb = $this->createQueryBuilder('eh')
            ->where('eh.esUbicacion = :esUbicacion')
            ->orWhere('eh.esUbicacion IS NULL')
            ->andWhere('eh.equipo = :equipo')
            ->setParameter('esUbicacion', false)
            ->setParameter('equipo', $equipo)
            ->OrderBy('eh.fechaInicio', 'DESC');

        if (isset($filtro["host"]) && $filtro["host"] != '') {
            $qb
                ->andWhere("eh.host LIKE :host")
                ->setParameter("host", '%' . $filtro["host"] . '%');
        }
        if (isset($filtro["ip"]) && $filtro["ip"] != '') {
            $qb
                ->andWhere("eh.ip LIKE :ip")
                ->setParameter("ip", '%' . $filtro["ip"] . '%');
        }
        if (!empty($filtro['fechaInicio'])) {
            $qb->andWhere('(eh.fechaInicio >= :fechaInicio OR eh.fechaInicio IS NULL)')
                ->setParameter('fechaInicio', $filtro['fechaInicio']);
        }

        // Filtro por fechaFin (permitiendo valores NULL)
        if (!empty($filtro['fechaFin'])) {
            $qb->andWhere('(eh.fechaFin <= :fechaFin OR eh.fechaFin IS NULL)')
                ->setParameter('fechaFin', $filtro['fechaFin']);
        }


        return $qb->getQuery();
    }


    // /**
    //  * @return EquipoHistorial[] Returns an array of EquipoHistorial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipoHistorial
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
