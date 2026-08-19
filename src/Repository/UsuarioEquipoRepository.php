<?php

namespace App\Repository;

use App\Entity\UsuarioEquipo;
use App\Entity\Equipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsuarioEquipo>
 *
 * @method UsuarioEquipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioEquipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioEquipo[]    findAll()
 * @method UsuarioEquipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioEquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioEquipo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UsuarioEquipo $entity, bool $flush = true): void
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
    public function remove(UsuarioEquipo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findForActionIndex($equipo, $filtro = [])
    {
        $qb = $this->createQueryBuilder('ue')
            ->where('ue.equipo = :equipo')
            ->setParameter('equipo', $equipo)
            ->orderBy('ue.id', 'DESC');

        if (!empty($filtro['fechaInicio'])) {
            $qb->andWhere('ue.fechaInicio >= :fechaInicio')
                ->setParameter('fechaInicio', $filtro['fechaInicio']);
        }
        if (!empty($filtro['fechaFin'])) {
            $qb->andWhere('ue.fechaFin <= :fechaFin')
                ->setParameter('fechaFin', $filtro['fechaFin']);
        }

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return UsuarioEquipo[] Returns an array of UsuarioEquipo objects
    //  */
    public function findByEquipo($us, $eq)
    {
        return $this->createQueryBuilder('p')
            ->join('p.usuario', 'u')
            ->andWhere('u.id = :usuario')
            ->setParameter('usuario', $us)
            ->join('p.equipo', 'e')
            ->andWhere('e.id = :equipo')
            ->setParameter('equipo', $eq)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Verifica si existe al menos un registro con isActual = true para un usuario/personal y equipo específicos
     *
     * @param int $usuarioId ID del usuario/personal
     * @param int $equipoId ID del equipo
     * @return bool Verdadero si existe al menos un registro, falso de lo contrario
     */
    public function usuarioActual(int $usuarioId, int $equipoId): bool
    {
        $qb = $this->createQueryBuilder('ue');
        $qb->select('COUNT(ue.id)')
            ->where('ue.usuario = :usuarioId')
            ->andWhere('ue.equipo = :equipoId')
            ->andWhere('ue.isActual = true')
            ->setParameter('usuarioId', $usuarioId)
            ->setParameter('equipoId', $equipoId);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Obtiene el historial de usuarios vinculados a un equipo donde isActual = false
     */
    public function findHistorialByEquipo(Equipo $equipo)
    {
        return $this->createQueryBuilder('ue')
            ->where('ue.equipo = :equipo')
            ->andWhere('ue.isActual = false')
            ->setParameter('equipo', $equipo)
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtiene todos los usuarios del equipo ordenados (actuales primero y luego por fecha)
     */
    public function findAllUsuariosByEquipoOrdered(Equipo $equipo): array
    {
        return $this->createQueryBuilder('ue')
            ->leftJoin('ue.usuario', 'u') // Hace join con la entidad Personal
            ->addSelect('u') // Para evitar el problema N+1
            ->where('ue.equipo = :equipo')
            ->setParameter('equipo', $equipo)
            ->orderBy('ue.isActual', 'DESC') // Actuales primero
            ->addOrderBy('ue.fechaInicio', 'DESC') // Más recientes primero
            ->getQuery()
            ->getResult();
    }
    /*
    public function findOneBySomeField($value): ?UsuarioEquipo
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
