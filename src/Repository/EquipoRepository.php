<?php

namespace App\Repository;

use App\Entity\Equipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Equipo>
 *
 * @method Equipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipo[]    findAll()
 * @method Equipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Equipo $entity, bool $flush = true): void
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
    public function remove(Equipo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function findForActionIndex($filtro = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect(
                "(CASE 
                        WHEN c.condicion = 1 THEN 1 
                        WHEN c.condicion = 2 THEN 2 
                        WHEN c.condicion IS NULL THEN 3
                        WHEN c.condicion = 3 THEN 4 
                        ELSE 5 
                        END) as HIDDEN orden"
            )
            ->join("c.tipo", "t")
            ->leftJoin("c.planillaEquipo", "pe")
            ->leftJoin("pe.almacenamientos", "a")
            ->leftJoin("pe.sistemasOperativos", "so")
            ->orderBy("orden", "ASC")
            ->addOrderBy("c.id", "DESC");

        if (isset($filtro["marca"]) && $filtro["marca"] != '') {
            $qb
                ->andWhere("pe.marca LIKE :marca")
                ->setParameter("marca", '%' . $filtro["marca"] . '%');
        }

        if (isset($filtro["modelo"]) && $filtro["modelo"] != '') {
            $qb
                ->andWhere("pe.modelo LIKE :modelo")
                ->setParameter("modelo", '%' . $filtro["modelo"] . '%');
        }

        if (isset($filtro["nroSerie"]) && $filtro["nroSerie"] != '') {
            $qb
                ->andWhere("pe.nroSerie LIKE :nroSerie")
                ->setParameter("nroSerie", '%' . $filtro["nroSerie"] . '%');
        }

        if (isset($filtro["nroInventario"]) && $filtro["nroInventario"] != '') {
            $qb
                ->andWhere("pe.nroInventario LIKE :nroInventario")
                ->setParameter("nroInventario", '%' . $filtro["nroInventario"] . '%');
        }

        if (isset($filtro["nombre"]) && $filtro["nombre"] != '') {
            $qb
                ->andWhere("c.nombre LIKE :nombre")
                ->setParameter("nombre", '%' . $filtro["nombre"] . '%');
        }

        if (isset($filtro["tipo"]) && $filtro["tipo"] != '') {
            $qb
                ->andWhere("c.tipo = :tipo")
                ->setParameter("tipo", $filtro["tipo"]);
        }

        if (!empty($filtro['ubicacion'])) {
            $qb->andWhere('c.ubicacion = :ubicacion')
                ->setParameter('ubicacion', $filtro['ubicacion']);
        }

        if (isset($filtro["condicion"]) && $filtro["condicion"] !== '') {
            if ($filtro["condicion"] == 4) {
                $qb->andWhere("c.condicion IS NULL");
            } else {
                $qb->andWhere("c.condicion = :condicion")
                    ->setParameter("condicion", $filtro["condicion"]);
            }
        }

        if (isset($filtro["id"]) && $filtro["id"] != '') {
            $qb
                ->andWhere("c.id LIKE :id")
                ->setParameter("id", '%' . $filtro["id"] . '%');
        }

        if (isset($filtro["mac"]) && $filtro["mac"] != '') {
            $qb
                ->andWhere("c.mac LIKE :mac")
                ->setParameter("mac", '%' . $filtro["mac"] . '%');
        }

        if (isset($filtro["ip"]) && $filtro["ip"] != '') {
            $qb
                ->andWhere("c.ip LIKE :ip")
                ->setParameter("ip", '%' . $filtro["ip"] . '%');
        }

        if (!empty($filtro['usuario'])) {
            $qb
                ->join('c.usuarios', 'u')
                ->andWhere('u.usuario = :usuario')
                ->andWhere('u.isActual = 1')
                ->setParameter('usuario', $filtro['usuario']);
        }
        if (isset($filtro["procesador"]) && $filtro["procesador"] != '') {
            $qb
                ->andWhere("pe.procesador LIKE :procesador")
                ->setParameter("procesador", '%' . $filtro["procesador"] . '%');
        }
        if (isset($filtro["memoriaRAM"]) && $filtro["memoriaRAM"] != '') {
            $qb->andWhere("pe.memoriaRAM = :memoriaRAM")
                ->setParameter("memoriaRAM", $filtro["memoriaRAM"]);
        }
        if (!empty($filtro['sistemaOperativo'])) {
            $qb->andWhere('so.nombre = :sistemaOperativo')
                ->setParameter('sistemaOperativo', $filtro['sistemaOperativo']);
        }
        if (!empty($filtro['tipoAlmacenamiento'])) {
            $qb->andWhere('a.tipo = :almacTipo')
                ->setParameter('almacTipo', $filtro['tipoAlmacenamiento']);
        }

        if (!empty($filtro['capacidadAlmacenamiento'])) {
            $qb->andWhere('a.capacidad = :almacCap')
                ->setParameter('almacCap', $filtro['capacidadAlmacenamiento']);
        }

        if (!empty($filtro['rolAlmacenamiento'])) {
            $qb->andWhere('a.rol = :almacRol')
                ->setParameter('almacRol', $filtro['rolAlmacenamiento']);
        }

        return $qb;
    }

    public function findByTipo($tipo)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.tipo = :tipo')
            ->setParameter('tipo', $tipo)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra el equipo asociado a una planilla por su ID.
     */
    public function findEquipoByPlanillaId(int $planillaId): ?Equipo
    {
        return $this->createQueryBuilder('e')
            ->join('e.planillaEquipo', 'p')
            ->where('p.id = :planillaEquipoId')
            ->setParameter('planillaEquipoId', $planillaId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findUltimoPorNomenclatura(string $nomenclatura): ?Equipo
    {
        return $this->createQueryBuilder('e')
            ->where('e.nombre LIKE :nomenclatura')
            ->setParameter('nomenclatura', $nomenclatura . '%')
            ->orderBy('e.nombre', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    // /**
    //  * @param array $tipos
    //  * @return Equipo[]
    //  */
    // public function findEquiposByTipo(array $tipos): array
    // {
    //     return $this->createQueryBuilder('e')
    //         ->leftJoin('e.tipo', 't')
    //         ->andWhere('t.nombre IN (:tipos)')
    //         ->setParameter('tipos', $tipos)
    //         ->getQuery()
    //         ->getResult();
    // }
    // /**
    //  * @return Equipo[] Returns an array of Equipo objects
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

    /** 
     *
    public function findOneBySomeField($value): ?Equipo
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
