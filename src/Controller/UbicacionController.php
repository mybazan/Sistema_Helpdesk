<?php

namespace App\Controller;

use App\Entity\Ubicacion;
use App\Entity\Personal;
use App\Entity\Pedido;
use App\Form\UbicacionType;
use App\Repository\UbicacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/ubicacion")
 */
class UbicacionController extends AbstractController
{
    /**
     * @Route("/", name="ubicacion_index", methods={"GET"})
     * @IsGranted("UBICACION_VER")
     */
    public function index(UbicacionRepository $ubicacionRepository): Response
    {
        return $this->render('ubicacion/index.html.twig', [
            'ubicaciones' => $ubicacionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ubicacion_new", methods={"GET", "POST"})
     * @IsGranted("UBICACION_CREAR")
     */
    public function new(Request $request, UbicacionRepository $ubicacionRepository): Response
    {
        $ubicacion = new Ubicacion();
        $ubicacion->habilitar();
        $form = $this->createForm(UbicacionType::class, $ubicacion, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ubicacion->habilitar();
            $ubicacionRepository->add($ubicacion, true);
            $this->addFlash('success', 'Ubicación creada correctamente.');
            return $this->redirectToRoute('ubicacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ubicacion/new.html.twig', [
            'ubicacion' => $ubicacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ubicacion_show", methods={"GET"})
     * @IsGranted("UBICACION_VER")
     */
    public function show(Ubicacion $ubicacion, EntityManagerInterface $em): Response
    {
        $personalCount = (int) $em->getRepository(Personal::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.ubicacion = :ubicacion')
            ->setParameter('ubicacion', $ubicacion)
            ->getQuery()
            ->getSingleScalarResult();

        $pedidoCount = (int) $em->getRepository(Pedido::class)->createQueryBuilder('pe')
            ->select('COUNT(pe.id)')
            ->where('pe.ubicacionPedido = :ubicacion')
            ->setParameter('ubicacion', $ubicacion)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('ubicacion/show.html.twig', [
            'ubicacion' => $ubicacion,
            'personalCount' => $personalCount,
            'pedidoCount' => $pedidoCount,
            'puedeEliminar' => count($ubicacion->getEquipos()) === 0 && $personalCount === 0 && $pedidoCount === 0,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ubicacion_edit", methods={"GET", "POST"})
     * @IsGranted("UBICACION_EDITAR")
     */
    public function edit(Request $request, Ubicacion $ubicacion, UbicacionRepository $ubicacionRepository): Response
    {
        $form = $this->createForm(UbicacionType::class, $ubicacion, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ubicacionRepository->add($ubicacion, true);
            $this->addFlash('success', 'Ubicación editada correctamente.');
            return $this->redirectToRoute('ubicacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ubicacion/edit.html.twig', [
            'ubicacion' => $ubicacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="ubicacion_delete", methods={"POST"})
     * @IsGranted("UBICACION_ELIMINAR")
     */
    public function delete(Request $request, Ubicacion $ubicacion, UbicacionRepository $ubicacionRepository, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ubicacion->getId(), $request->request->get('_token'))) {
            if (count($ubicacion->getEquipos()) > 0) {
                $this->addFlash('danger', 'No se puede eliminar esta ubicación porque tiene equipos asociados.');
                return $this->redirectToRoute('ubicacion_show', ['id' => $ubicacion->getId()], Response::HTTP_SEE_OTHER);
            }

            $personalCount = (int) $em->getRepository(Personal::class)->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->where('p.ubicacion = :ubicacion')
                ->setParameter('ubicacion', $ubicacion)
                ->getQuery()
                ->getSingleScalarResult();

            if ($personalCount > 0) {
                $this->addFlash('danger', 'No se puede eliminar esta ubicación porque tiene personal asociado.');
                return $this->redirectToRoute('ubicacion_show', ['id' => $ubicacion->getId()], Response::HTTP_SEE_OTHER);
            }

            $pedidoCount = (int) $em->getRepository(Pedido::class)->createQueryBuilder('pe')
                ->select('COUNT(pe.id)')
                ->where('pe.ubicacionPedido = :ubicacion')
                ->setParameter('ubicacion', $ubicacion)
                ->getQuery()
                ->getSingleScalarResult();

            if ($pedidoCount > 0) {
                $this->addFlash('danger', 'No se puede eliminar esta ubicación porque tiene tickets asociados.');
                return $this->redirectToRoute('ubicacion_show', ['id' => $ubicacion->getId()], Response::HTTP_SEE_OTHER);
            }

            $ubicacionRepository->remove($ubicacion, true);
            $this->addFlash('success', 'Ubicación eliminada correctamente.');
        }

        return $this->redirectToRoute('ubicacion_index', [], Response::HTTP_SEE_OTHER);
    }
}
