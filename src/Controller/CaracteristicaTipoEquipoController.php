<?php

namespace App\Controller;

use App\Entity\CaracteristicaTipoEquipo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Flujo legacy deshabilitado: las especificaciones se gestionan vía Planilla del Equipo.
 *
 * @Route("/caracteristica/tipo/equipo")
 */
class CaracteristicaTipoEquipoController extends AbstractController
{
    /**
     * @Route("/", name="caracteristica_tipo_equipo_index", methods={"GET"})
     * @IsGranted("TIPO_EQUIPO_VER")
     */
    public function index(): Response
    {
        return $this->redirectToTipoEquipo();
    }

    /**
     * @Route("/new", name="caracteristica_tipo_equipo_new", methods={"GET", "POST"})
     * @IsGranted("TIPO_EQUIPO_CREAR")
     */
    public function new(Request $request): Response
    {
        return $this->redirectToTipoEquipo($request->get('idE'));
    }

    /**
     * @Route("/{id}", name="caracteristica_tipo_equipo_show", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("TIPO_EQUIPO_VER")
     */
    public function show(CaracteristicaTipoEquipo $caracteristicaTipoEquipo): Response
    {
        $tipoId = $caracteristicaTipoEquipo->getTipo() ? $caracteristicaTipoEquipo->getTipo()->getId() : null;

        return $this->redirectToTipoEquipo($tipoId);
    }

    /**
     * @Route("/{id}/edit", name="caracteristica_tipo_equipo_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("TIPO_EQUIPO_EDITAR")
     */
    public function edit(CaracteristicaTipoEquipo $caracteristicaTipoEquipo): Response
    {
        $tipoId = $caracteristicaTipoEquipo->getTipo() ? $caracteristicaTipoEquipo->getTipo()->getId() : null;

        return $this->redirectToTipoEquipo($tipoId);
    }

    /**
     * @Route("/{id}", name="caracteristica_tipo_equipo_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("TIPO_EQUIPO_ELIMINAR")
     */
    public function delete(CaracteristicaTipoEquipo $caracteristicaTipoEquipo): Response
    {
        $tipoId = $caracteristicaTipoEquipo->getTipo() ? $caracteristicaTipoEquipo->getTipo()->getId() : null;

        return $this->redirectToTipoEquipo($tipoId);
    }

    private function redirectToTipoEquipo($tipoId = null): Response
    {
        $this->addFlash(
            'info',
            'Las especificaciones técnicas se cargan en la Planilla de cada equipo, no en el tipo.'
        );

        if ($tipoId) {
            return $this->redirectToRoute('tipo_equipo_show', ['id' => $tipoId], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('tipo_equipo_index', [], Response::HTTP_SEE_OTHER);
    }
}
