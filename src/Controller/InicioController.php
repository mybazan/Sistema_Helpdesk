<?php

namespace App\Controller;

use App\Repository\PedidoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    /**
     * @Route("/",name="inicio_index")
     * @IsGranted("VER_INICIO")
     */
    public function index(Request $request, PedidoRepository $pedidoRepository)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $tecnicoId = null;
        if (!$this->isGranted('TICKET_ASIGNAR') && $this->getUser()->getIsTecnico()) {
            $tecnicoId = $this->getUser()->getId();
        }

        $conteoEstados = [];
        $sinAsignar = 0;
        $totalActivos = 0;

        if ($this->isGranted('TICKET_VER')) {
            $conteoEstados = $pedidoRepository->countByEstadoActual($tecnicoId);
            if ($this->isGranted('TICKET_ASIGNAR')) {
                $sinAsignar = $pedidoRepository->countSinAsignar();
            }

            foreach ($conteoEstados as $estado => $total) {
                if (!in_array($estado, ['Finalizado', 'Desestimado'], true)) {
                    $totalActivos += $total;
                }
            }
        }

        return $this->render('inicio/index.html.twig', [
            'conteoEstados' => $conteoEstados,
            'sinAsignar' => $sinAsignar,
            'totalActivos' => $totalActivos,
        ]);
    }

    /**
     * @Route("/ayuda",name="inicio_ayuda")
     */
    public function help()
    {
        return $this->render('inicio/help.html.twig');
    }
}
