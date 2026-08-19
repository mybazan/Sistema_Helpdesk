<?php

namespace App\Controller;

use App\Entity\Caracteristica;
use App\Entity\Equipo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Flujo legacy deshabilitado: las especificaciones se gestionan vía Planilla del Equipo.
 *
 * @Route("/caracteristica")
 */
class CaracteristicaController extends AbstractController
{
    /**
     * @Route("/", name="caracteristica_index", methods={"GET"})
     * @IsGranted("EQUIPO_VER")
     */
    public function index(Request $request): Response
    {
        return $this->redirectToPlanilla($request->query->get('id'));
    }

    /**
     * @Route("/new", name="caracteristica_new", methods={"GET", "POST"})
     * @IsGranted("EQUIPO_CREAR")
     */
    public function new(Request $request): Response
    {
        return $this->redirectToPlanilla($request->query->get('id') ?? $request->get('id'));
    }

    /**
     * @Route("/{id}", name="caracteristica_show", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("EQUIPO_VER")
     */
    public function show(Caracteristica $caracteristica): Response
    {
        return $this->redirectToPlanilla($caracteristica->getEquipo() ? $caracteristica->getEquipo()->getId() : null);
    }

    /**
     * @Route("/new_caracteristica", name="equipo_caracteristica_new", methods={"POST"})
     * @IsGranted("EQUIPO_VER")
     */
    public function create(Request $request): Response
    {
        return $this->redirectToPlanilla($request->get('idE'));
    }

    /**
     * @Route("/{id}/edit", name="caracteristica_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function edit(Caracteristica $caracteristica): Response
    {
        return $this->redirectToPlanilla($caracteristica->getEquipo() ? $caracteristica->getEquipo()->getId() : null);
    }

    /**
     * @Route("/{id}", name="caracteristica_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("EQUIPO_ELIMINAR")
     */
    public function delete(Caracteristica $caracteristica): Response
    {
        return $this->redirectToPlanilla($caracteristica->getEquipo() ? $caracteristica->getEquipo()->getId() : null);
    }

    /**
     * @Route("/new_caracteristica_complete/{id}", name="equipo_caracteristica_new_complete", methods={"POST"})
     * @IsGranted("EQUIPO_EDITAR")
     */
    public function createCaracteristicaComplete(Equipo $equipo): Response
    {
        return $this->redirectToPlanilla($equipo->getId());
    }

    private function redirectToPlanilla($equipoId): Response
    {
        $this->addFlash(
            'info',
            'Las especificaciones del equipo se gestionan desde la Planilla del Equipo.'
        );

        if ($equipoId) {
            return $this->redirectToRoute('show_equipo', ['id' => $equipoId], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('index_equipo', [], Response::HTTP_SEE_OTHER);
    }
}
