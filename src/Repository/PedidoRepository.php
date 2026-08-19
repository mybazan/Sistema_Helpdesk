<?php

namespace App\Repository;

use App\Entity\Pedido;
use App\Entity\PedidoHistorialEstado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Pedido>
 *
 * @method Pedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pedido[]    findAll()
 * @method Pedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedidoRepository extends ServiceEntityRepository
{
    private $security;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
    }

    public function findForActionIndex($filtros = [])
    {
        $qb = $this->createQueryBuilder('p')
                ->leftJoin('p.tecnicoAsignado', 't')
                ->leftJoin('p.pedidoHistorialEstados', 'phe')
                ->addSelect('phe')
                ->leftJoin('phe.pedidoEstado', 'pe')
                ->addSelect('pe')
                ->where('phe.fecha = (
                    SELECT MAX(phe2.fecha)
                    FROM App\Entity\PedidoHistorialEstado phe2
                    WHERE phe2.pedido = p
                    )')
                ->orderBy('p.fecha','DESC')
        ;

        if(isset($filtros["asignado"]) && $filtros["asignado"] != ''){
            $qb
                ->andWhere("t.id = :tecnicoId")
                ->setParameter("tecnicoId", $filtros["asignado"]);
            ;
        }
        
        if(isset($filtros["id"]) && $filtros["id"] != ''){
            $qb
                ->andWhere("p.id LIKE :id")
                ->setParameter("id", '%'.$filtros["id"].'%');
        }
        
        if(isset($filtros["fecha"]) && $filtros["fecha"] != '') {
            $fechaFormateada = $filtros["fecha"]->format('Y-m-d');
            $qb
                ->andWhere("p.fecha >= :fechaInicio")
                ->setParameter("fechaInicio", $fechaFormateada.' 00:00:00');
            $qb
                ->andWhere("p.fecha <= :fechaFin")
                ->setParameter("fechaFin", $fechaFormateada.' 23:59:59');
        }

        if(isset($filtros["solicitanteTexto"]) && $filtros["solicitanteTexto"] != ''){
            $qb
                ->andWhere("p.solicitanteTexto LIKE :solicitanteTexto")
                ->setParameter("solicitanteTexto", '%'.$filtros["solicitanteTexto"].'%');
        }

        if(isset($filtros["ubicacionTexto"]) && $filtros["ubicacionTexto"] != ''){
            $qb
                ->andWhere("p.ubicacionTexto LIKE :ubicacionTexto")
                ->setParameter("ubicacionTexto", '%'.$filtros["ubicacionTexto"].'%');
        }
        
        if(isset($filtros["solicitud"]) && $filtros["solicitud"] != ''){
            $qb
                ->andWhere("p.solicitud LIKE :solicitud")
                ->setParameter("solicitud", '%'.$filtros["solicitud"].'%');
        }
        
        if(isset($filtros["personal"]) && $filtros["personal"] != ''){
            $personal = $filtros["personal"];
            $personalId = is_object($personal) ? $personal->getId() : $personal;
            $qb
                ->andWhere("t.id = :personal")
                ->setParameter("personal", $personalId);
        }

        if(isset($filtros["prioridad"]) && $filtros["prioridad"] != ''){
            $qb
                ->andWhere("p.prioridad = :prioridad")
                ->setParameter("prioridad", $filtros["prioridad"]);
        }
        
        if(isset($filtros["estado"]) && $filtros["estado"] != ''){
            $estado = $filtros["estado"];
            $estadoId = is_object($estado) ? $estado->getId() : $estado;
            $qb
                ->andWhere("pe.id = :estado")
                ->setParameter("estado", $estadoId);
        }

        return $qb;
    }

    public function countByEstadoActual(?int $tecnicoId = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('pe.nombre AS estado, COUNT(p.id) AS total')
            ->leftJoin('p.tecnicoAsignado', 't')
            ->leftJoin('p.pedidoHistorialEstados', 'phe')
            ->leftJoin('phe.pedidoEstado', 'pe')
            ->where('phe.fecha = (
                SELECT MAX(phe2.fecha)
                FROM App\Entity\PedidoHistorialEstado phe2
                WHERE phe2.pedido = p
            )')
            ->groupBy('pe.nombre')
            ->orderBy('pe.nombre', 'ASC');

        if ($tecnicoId) {
            $qb
                ->andWhere('t.id = :tecnicoId')
                ->setParameter('tecnicoId', $tecnicoId);
        }

        $result = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $result[$row['estado']] = (int) $row['total'];
        }

        return $result;
    }

    public function countSinAsignar(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.pedidoHistorialEstados', 'phe')
            ->leftJoin('phe.pedidoEstado', 'pe')
            ->where('phe.fecha = (
                SELECT MAX(phe2.fecha)
                FROM App\Entity\PedidoHistorialEstado phe2
                WHERE phe2.pedido = p
            )')
            ->andWhere('p.tecnicoAsignado IS NULL')
            ->andWhere('pe.nombre = :estado')
            ->setParameter('estado', 'Recibido')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getExisteEquipoOperativo($id)
    {
        $cantidad = $this->createQueryBuilder('p')
                        ->select('COUNT(p.id)')    
                        ->leftJoin('p.pedidoTecnicoAsignados', 'pta')
                        ->where('p.id = '.$id)
                        ->andWhere('pta.esOperativo = true')
                        ->getQuery()
                        ->getSingleScalarResult();
        return ($cantidad > 0 ? true : false);

    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Pedido $entity, bool $flush = true): void
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
    public function remove(Pedido $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Pedido[] Returns an array of Pedido objects
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
    public function findOneBySomeField($value): ?Pedido
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
